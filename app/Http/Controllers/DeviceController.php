<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceArchive;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DeviceController extends Controller
{
    /**
     * Display a listing of the devices.
     */
    public function index()
    {
        $devices = Device::with('manufacturer')->orderBy('created_at', 'desc')->paginate(15);
        
        return view('devices.index', compact('devices'));
    }

    /**
     * Show the form for importing a new device.
     */
    public function create()
    {
        $manufacturers = Manufacturer::orderBy('name')->get();
        
        return view('devices.create', compact('manufacturers'));
    }

    /**
     * Store a newly created device in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => ['required', 'string', 'max:255'],
            'bar_code' => ['nullable', 'string', 'max:255'],
            'manufacturer_id' => ['nullable', 'exists:manufacturers,id'],
            'model' => ['required', 'string', 'max:255'],
            'cost_excl' => ['nullable', 'numeric', 'min:0'],
            'cost_incl' => ['nullable', 'numeric', 'min:0'],
            'rsp_excl' => ['nullable', 'numeric', 'min:0'],
            'rsp_incl' => ['nullable', 'numeric', 'min:0'],
        ]);

        Device::create($validated);

        return redirect()->route('devices.index')
            ->with('success', 'Device imported successfully.');
    }

    /**
     * Show the form for importing devices from Excel.
     */
    public function importForm()
    {
        return view('devices.import');
    }

    /**
     * Import devices from Excel file.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'], // 10MB max
        ]);

        try {
            DB::beginTransaction();

            // Generate unique batch number
            $batchNumber = 'BATCH-' . date('YmdHis') . '-' . uniqid();

            // Get all current devices and move to archive
            $currentDevices = Device::all();
            if ($currentDevices->count() > 0) {
                foreach ($currentDevices as $device) {
                    DeviceArchive::create([
                        'product_code' => $device->product_code,
                        'bar_code' => $device->bar_code,
                        'manufacturer_id' => $device->manufacturer_id,
                        'model' => $device->model,
                        'cost_excl' => $device->cost_excl,
                        'cost_incl' => $device->cost_incl,
                        'rsp_excl' => $device->rsp_excl,
                        'rsp_incl' => $device->rsp_incl,
                        'batch_number' => $batchNumber,
                        'created_at' => $device->created_at,
                        'updated_at' => $device->updated_at,
                    ]);
                }
            }

            // Clear current devices table
            Device::truncate();

            // Read Excel file
            $file = $request->file('excel_file');
            $data = Excel::toArray([], $file);

            if (empty($data) || empty($data[0])) {
                throw new \Exception('Excel file is empty or invalid.');
            }

            $rows = $data[0];
            $headerRow = array_shift($rows); // Remove header row

            $imported = 0;
            $errors = [];
            $manufacturersCreated = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed

                try {
                    // Map Excel columns to data
                    // Expected: Product Code | Bar Code | Make | Model | Cost Excl | Cost Incl | RSP Excl | RSP Incl
                    $productCode = $row[0] ?? null;
                    $barCode = $row[1] ?? null;
                    $make = $row[2] ?? null;
                    $model = $row[3] ?? null;
                    $costExcl = $row[4] ?? null;
                    $costIncl = $row[5] ?? null;
                    $rspExcl = $row[6] ?? null;
                    $rspIncl = $row[7] ?? null;

                    // Skip empty rows
                    if (empty($productCode) && empty($model)) {
                        continue;
                    }

                    // Validate required fields
                    if (empty($productCode)) {
                        throw new \Exception("Product Code is required");
                    }
                    if (empty($model)) {
                        throw new \Exception("Model is required");
                    }

                    // Find or create manufacturer (automatically creates if doesn't exist)
                    $manufacturerId = null;
                    if (!empty($make)) {
                        $makeName = trim($make);
                        $manufacturer = Manufacturer::firstOrCreate(
                            ['name' => $makeName],
                            ['name' => $makeName]
                        );
                        
                        // Track if manufacturer was just created
                        if ($manufacturer->wasRecentlyCreated && !in_array($makeName, $manufacturersCreated)) {
                            $manufacturersCreated[] = $makeName;
                        }
                        
                        $manufacturerId = $manufacturer->id;
                    }

                    // Create device
                    Device::create([
                        'product_code' => trim($productCode),
                        'bar_code' => !empty($barCode) ? trim($barCode) : null,
                        'manufacturer_id' => $manufacturerId,
                        'model' => trim($model),
                        'cost_excl' => !empty($costExcl) && is_numeric($costExcl) ? (float) $costExcl : null,
                        'cost_incl' => !empty($costIncl) && is_numeric($costIncl) ? (float) $costIncl : null,
                        'rsp_excl' => !empty($rspExcl) && is_numeric($rspExcl) ? (float) $rspExcl : null,
                        'rsp_incl' => !empty($rspIncl) && is_numeric($rspIncl) ? (float) $rspIncl : null,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Import completed successfully. {$imported} device(s) imported.";
            if (!empty($manufacturersCreated)) {
                $message .= " " . count($manufacturersCreated) . " new manufacturer(s) created: " . implode(', ', $manufacturersCreated) . ".";
            }
            if (!empty($errors)) {
                $message .= " " . count($errors) . " error(s) occurred.";
            }

            return redirect()->route('devices.index')
                ->with('success', $message)
                ->with('import_errors', $errors)
                ->with('batch_number', $batchNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('devices.import')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
