<?php

namespace App\Http\Controllers;

use App\Models\RepairSetting;
use Illuminate\Http\Request;

class RepairSettingsController extends Controller
{
    /**
     * Show the repairs settings form.
     */
    public function edit()
    {
        $settings = RepairSetting::get();

        return view('settings.repairs', compact('settings'));
    }

    /**
     * Update the repairs settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'repair_form_terms' => ['nullable', 'string'],
            'repair_invoice_terms' => ['nullable', 'string'],
        ]);

        $settings = RepairSetting::get();
        $settings->update($validated);

        return redirect()->route('settings.repairs.edit')
            ->with('success', 'Repair settings saved successfully.');
    }
}
