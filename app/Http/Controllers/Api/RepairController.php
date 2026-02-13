<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Repair;
use App\Models\RepairEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    /**
     * List all repairs (paginated). For React Native app.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 20), 100);

        $repairs = Repair::with(['customer', 'allocatedTo'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($repairs);
    }

    /**
     * Show a single repair with customer, allocatedTo, and events.
     */
    public function show(Repair $repair): JsonResponse
    {
        $repair->load(['customer', 'allocatedTo', 'events.user']);

        return response()->json($repair);
    }

    /**
     * Store a new repair. Records a "created" event.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'phone' => ['nullable', 'string', 'max:255'],
            'imei' => ['nullable', 'string', 'max:20'],
            'cell_nr' => ['nullable', 'string', 'max:255'],
            'contact_nr' => ['nullable', 'string', 'max:255'],
            'allocated_to' => ['nullable', 'integer', 'exists:external_users,id'],
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

        $repair->load(['customer', 'allocatedTo']);

        return response()->json($repair, 201);
    }

    /**
     * Update an existing repair. Records a status_updated event if status changed.
     */
    public function update(Request $request, Repair $repair): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'phone' => ['nullable', 'string', 'max:255'],
            'imei' => ['nullable', 'string', 'max:20'],
            'cell_nr' => ['nullable', 'string', 'max:255'],
            'contact_nr' => ['nullable', 'string', 'max:255'],
            'allocated_to' => ['nullable', 'integer', 'exists:external_users,id'],
            'fault_description' => ['nullable', 'string', 'max:5000'],
            'ticket_status' => ['nullable', 'string', 'in:booked_in,sent_away,received,completed,collected'],
        ]);

        $oldStatus = $repair->ticket_status;
        $repair->update($validated);

        if (isset($validated['ticket_status']) && $validated['ticket_status'] !== $oldStatus) {
            RepairEvent::create([
                'repair_id' => $repair->id,
                'event_type' => 'status_updated',
                'description' => 'Status changed from ' . (Repair::STATUS_LABELS[$oldStatus] ?? $oldStatus) . ' to ' . $repair->status_label,
                'meta' => [
                    'old_status' => $oldStatus,
                    'new_status' => $repair->ticket_status,
                ],
                'user_id' => auth()->id(),
            ]);
        }

        $repair->load(['customer', 'allocatedTo']);

        return response()->json($repair);
    }
}
