<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\LoanDevice;
use Illuminate\Http\Request;

class LoanDeviceController extends Controller
{
    /**
     * List all loan devices.
     */
    public function index()
    {
        $loanDevices = LoanDevice::with('device.manufacturer', 'repair.customer')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('loan-devices.index', compact('loanDevices'));
    }

    /**
     * Show the form for creating a loan device.
     */
    public function create()
    {
        $devices = Device::with('manufacturer')->orderBy('model')->get();

        return view('loan-devices.create', compact('devices'));
    }

    /**
     * Store a new loan device.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => ['required', 'integer', 'exists:devices,id'],
            'imei' => ['nullable', 'string', 'max:50'],
        ]);

        LoanDevice::create($validated);

        return redirect()->route('loan-devices.index')
            ->with('success', 'Loan device added successfully.');
    }

    /**
     * Show the form for editing a loan device.
     */
    public function edit(LoanDevice $loanDevice)
    {
        $loanDevice->load('device.manufacturer');
        $devices = Device::with('manufacturer')->orderBy('model')->get();

        return view('loan-devices.edit', compact('loanDevice', 'devices'));
    }

    /**
     * Update the specified loan device.
     */
    public function update(Request $request, LoanDevice $loanDevice)
    {
        $validated = $request->validate([
            'device_id' => ['required', 'integer', 'exists:devices,id'],
            'imei' => ['nullable', 'string', 'max:50'],
        ]);

        $loanDevice->update($validated);

        return redirect()->route('loan-devices.index')
            ->with('success', 'Loan device updated successfully.');
    }

    /**
     * Remove the specified loan device.
     */
    public function destroy(LoanDevice $loanDevice)
    {
        $loanDevice->delete();

        return redirect()->route('loan-devices.index')
            ->with('success', 'Loan device removed.');
    }
}
