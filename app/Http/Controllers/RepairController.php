<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ExternalUser;
use App\Models\LoanDevice;
use App\Models\Repair;
use App\Models\RepairEvent;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    /**
     * Display a listing of repairs.
     */
    public function index()
    {
        $repairs = Repair::with(['customer', 'allocatedTo', 'loanDevice.device.manufacturer'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('repairs.index', compact('repairs'));
    }

    /**
     * Show the form for creating a new repair.
     */
    public function create()
    {
        $customers = Customer::orderBy('surname')->orderBy('name')->get();
        $externalUsers = ExternalUser::orderBy('company')->orderBy('surname')->orderBy('name')->get();
        $loanDevices = LoanDevice::with('device.manufacturer')->orderByDesc('created_at')->get();

        return view('repairs.create', [
            'customers' => $customers,
            'externalUsers' => $externalUsers,
            'loanDevices' => $loanDevices,
        ]);
    }

    /**
     * Store a newly created repair and record "created" event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'phone' => ['nullable', 'string', 'max:255'],
            'imei' => ['nullable', 'string', 'max:20'],
            'cell_nr' => ['nullable', 'string', 'max:255'],
            'contact_nr' => ['nullable', 'string', 'max:255'],
            'allocated_to' => ['nullable', 'integer', 'exists:external_users,id'],
            'loan_device_id' => ['nullable', 'integer', 'exists:loan_devices,id'],
            'fault_description' => ['nullable', 'string', 'max:5000'],
            'ticket_status' => ['nullable', 'string', 'in:booked_in,sent_away,received,completed,collected'],
        ]);

        $validated['ticket_status'] = $validated['ticket_status'] ?? Repair::STATUS_BOOKED_IN;

        $repair = Repair::create($validated);

        RepairEvent::create([
            'repair_id' => $repair->id,
            'event_type' => 'created',
            'description' => 'Repair ticket created.',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Repair ticket created successfully.');
    }

    /**
     * Display the specified repair with event history.
     */
    public function show(Repair $repair)
    {
        $repair->load(['customer', 'allocatedTo', 'loanDevice.device.manufacturer', 'events.user']);
        $externalUsers = ExternalUser::orderBy('company')->orderBy('surname')->orderBy('name')->get();
        $loanDevices = LoanDevice::with('device.manufacturer')->orderByDesc('created_at')->get();

        return view('repairs.show', compact('repair', 'externalUsers', 'loanDevices'));
    }

    /**
     * Show the form for editing the specified repair.
     */
    public function edit(Repair $repair)
    {
        $customers = Customer::orderBy('surname')->orderBy('name')->get();
        $externalUsers = ExternalUser::orderBy('company')->orderBy('surname')->orderBy('name')->get();
        $loanDevices = LoanDevice::with('device.manufacturer')->orderByDesc('created_at')->get();

        return view('repairs.edit', [
            'repair' => $repair,
            'customers' => $customers,
            'externalUsers' => $externalUsers,
            'loanDevices' => $loanDevices,
        ]);
    }

    /**
     * Update the specified repair; record status change event if status changed.
     */
    public function update(Request $request, Repair $repair)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'phone' => ['nullable', 'string', 'max:255'],
            'imei' => ['nullable', 'string', 'max:20'],
            'cell_nr' => ['nullable', 'string', 'max:255'],
            'contact_nr' => ['nullable', 'string', 'max:255'],
            'allocated_to' => ['nullable', 'integer', 'exists:external_users,id'],
            'loan_device_id' => ['nullable', 'integer', 'exists:loan_devices,id'],
            'fault_description' => ['nullable', 'string', 'max:5000'],
            'ticket_status' => ['nullable', 'string', 'in:booked_in,sent_away,received,completed,collected'],
        ]);

        $oldStatus = $repair->ticket_status;
        $repair->update($validated);

        if (isset($validated['ticket_status']) && $validated['ticket_status'] !== $oldStatus) {
            RepairEvent::create([
                'repair_id' => $repair->id,
                'event_type' => 'status_updated',
                'description' => 'Status changed from ' . (Repair::STATUS_LABELS[$oldStatus] ?? $oldStatus) . ' to ' . ($repair->status_label),
                'meta' => [
                    'old_status' => $oldStatus,
                    'new_status' => $repair->ticket_status,
                ],
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Repair ticket updated successfully.');
    }

    /**
     * Update only the ticket status (from "Update status" button); adds history item.
     */
    public function updateStatus(Request $request, Repair $repair)
    {
        $validated = $request->validate([
            'ticket_status' => ['required', 'string', 'in:booked_in,sent_away,received,completed,collected'],
            'allocated_to' => ['nullable', 'integer', 'exists:external_users,id'],
        ]);

        $oldStatus = $repair->ticket_status;
        $newStatus = $validated['ticket_status'];

        $data = ['ticket_status' => $newStatus];
        if ($newStatus === Repair::STATUS_SENT_AWAY && array_key_exists('allocated_to', $validated)) {
            $data['allocated_to'] = $validated['allocated_to'];
        }

        if ($newStatus === $oldStatus && empty(array_diff_assoc($data, $repair->only(array_keys($data))))) {
            return redirect()->route('repairs.show', $repair)
                ->with('success', 'Status unchanged.');
        }

        $repair->update($data);

        $description = 'Status changed from ' . (Repair::STATUS_LABELS[$oldStatus] ?? $oldStatus) . ' to ' . (Repair::STATUS_LABELS[$newStatus] ?? $newStatus);
        if ($newStatus === Repair::STATUS_SENT_AWAY && $repair->allocatedTo) {
            $description .= ' (sent to ' . ($repair->allocatedTo->company ?: $repair->allocatedTo->name) . ')';
        }

        RepairEvent::create([
            'repair_id' => $repair->id,
            'event_type' => 'status_updated',
            'description' => $description,
            'meta' => [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('repairs.show', $repair)
            ->with('success', 'Status updated successfully.');
    }

    /**
     * Remove the specified repair (optional; not in initial scope).
     */
    public function destroy(Repair $repair)
    {
        $repair->delete();

        return redirect()->route('repairs.index')
            ->with('success', 'Repair ticket deleted.');
    }
}
