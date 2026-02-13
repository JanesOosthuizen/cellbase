<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Imei;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index()
    {
        $invoices = Invoice::with('invoiceLines.device.manufacturer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $devices = Device::with('manufacturer')->orderBy('product_code')->get();
        
        return view('invoices.create', compact('devices'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_date' => ['required', 'date'],
            'invoice_nr' => ['required', 'string', 'max:20', 'unique:invoices,invoice_nr', 'regex:/^[A-Z]\d{5}$/'],
            'invoice_total_excl' => ['required', 'numeric', 'min:0'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.device_id' => ['nullable', 'exists:devices,id'],
            'lines.*.imei' => ['nullable', 'string', 'size:15', Rule::unique('invoice_lines', 'imei')->ignore(null)],
            'lines.*.is_imei' => ['nullable', 'boolean'],
            'lines.*.price' => ['required', 'numeric', 'min:0'],
        ], [
            'invoice_nr.regex' => 'Invoice number must be in format X00000 (one letter followed by 5 digits).',
            'lines.*.imei.size' => 'IMEI must be exactly 15 characters.',
            'lines.*.imei.unique' => 'This IMEI has already been used.',
        ]);

        // Additional validation: Check IMEI uniqueness manually for non-null values
        $imeis = [];
        foreach ($validated['lines'] as $index => $line) {
            if (!empty($line['imei'])) {
                if (in_array($line['imei'], $imeis)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Duplicate IMEI found in line " . ($index + 1) . ": " . $line['imei']);
                }
                $imeis[] = $line['imei'];
                
                // Check if IMEI already exists in database
                if (InvoiceLine::where('imei', $line['imei'])->exists()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "IMEI already exists: " . $line['imei']);
                }
            }
        }

        try {
            DB::beginTransaction();

            // Create invoice as draft
            $invoice = Invoice::create([
                'invoice_date' => $validated['invoice_date'],
                'invoice_nr' => strtoupper($validated['invoice_nr']),
                'invoice_total_excl' => $validated['invoice_total_excl'],
                'status' => 'draft',
            ]);

            // Create invoice lines
            foreach ($validated['lines'] as $line) {
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'device_id' => $line['device_id'] ?? null,
                    'imei' => !empty($line['imei']) ? $line['imei'] : null,
                    'is_imei' => $line['is_imei'] ?? false,
                    'price' => $line['price'],
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'Invoice created as draft. Please approve it to finalize.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Approve an invoice and create IMEI records.
     */
    public function approve(Invoice $invoice)
    {
        if ($invoice->status === 'approved') {
            return redirect()->route('invoices.index')
                ->with('error', 'Invoice is already approved.');
        }

        try {
            DB::beginTransaction();

            // Update invoice status
            $invoice->update(['status' => 'approved']);

            // Create IMEI records from invoice lines
            foreach ($invoice->invoiceLines as $line) {
                $device = $line->device;
                
                Imei::create([
                    'date' => $invoice->invoice_date,
                    'invoice' => $invoice->invoice_nr,
                    'invoiceId' => (string) $invoice->id,
                    'phone' => $device ? ($device->manufacturer ? $device->manufacturer->name . ' ' . $device->model : $device->model) : null,
                    'phone_stock_code' => $device ? $device->product_code : null,
                    'imei' => $line->imei,
                    'nonImei' => $line->is_imei ? 0 : 1,
                    'price' => $line->price,
                    'allocatedTo' => null,
                    'number' => null,
                    'name' => null,
                    'activationDate' => null,
                    'DealSheetNr' => null,
                    'upgradeContract' => null,
                    'company' => null,
                    'entryAddedDate' => now(),
                    'entryModifiedDate' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'Invoice approved and IMEI records created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('invoices.index')
                ->with('error', 'Failed to approve invoice: ' . $e->getMessage());
        }
    }
}
