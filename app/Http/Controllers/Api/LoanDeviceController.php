<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanDevice;
use App\Models\Repair;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanDeviceController extends Controller
{
    /**
     * List loan devices. Optional: q or imei (search by IMEI, partial match), per_page.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 50), 100);
        $q = $request->input('q') ?? $request->input('imei');

        $query = LoanDevice::query()
            ->with(['device.manufacturer', 'repair.customer']);

        if ($q && is_string($q) && trim($q) !== '') {
            $term = '%' . trim($q) . '%';
            $query->where('imei', 'like', $term);
        }

        $loanDevices = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($loanDevices);
    }

    /**
     * Allocate this loan device to a repair (and thus to that repair's customer).
     * Body: { "repair_id": 123 }
     * Any other repair that had this device will be unlinked.
     */
    public function allocate(Request $request, LoanDevice $loanDevice): JsonResponse
    {
        $validated = $request->validate([
            'repair_id' => ['required', 'integer', 'exists:repairs,id'],
        ]);

        $repair = Repair::findOrFail($validated['repair_id']);

        // Unallocate from any repair that currently has this loan device
        Repair::where('loan_device_id', $loanDevice->id)->update(['loan_device_id' => null]);

        // Allocate to the chosen repair
        $repair->update(['loan_device_id' => $loanDevice->id]);

        $repair->load(['customer', 'loanDevice.device.manufacturer']);

        return response()->json([
            'message' => 'Loan device allocated to repair.',
            'repair' => $repair,
        ]);
    }
}
