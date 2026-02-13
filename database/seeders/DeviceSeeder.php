<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Manufacturer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/devices.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found: {$csvPath}");
            return;
        }

        $this->command->info('Reading CSV file...');
        
        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->command->error("Could not open CSV file: {$csvPath}");
            return;
        }

        // Read header row
        $headers = fgetcsv($file);
        if (!$headers) {
            $this->command->error("Could not read CSV headers");
            fclose($file);
            return;
        }

        // Map CSV columns to array indices
        $columnMap = [];
        foreach ($headers as $index => $header) {
            $columnMap[trim($header, '"')] = $index;
        }

        $this->command->info('Processing devices...');
        $processed = 0;
        $errors = 0;

        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($file)) !== false) {
                try {
                    // Get manufacturer name and find/create manufacturer
                    $manufacturerName = trim($row[$columnMap['manufacturer']] ?? '', '" ');
                    if (empty($manufacturerName)) {
                        $manufacturerName = 'Unknown';
                    }
                    
                    $manufacturer = Manufacturer::firstOrCreate(
                        ['name' => $manufacturerName]
                    );

                    // Create device
                    Device::create([
                        'product_code' => trim($row[$columnMap['product_code']] ?? '', '" '),
                        'manufacturer_id' => $manufacturer->id,
                        'model' => trim($row[$columnMap['model']] ?? '', '" '),
                        'bar_code' => trim($row[$columnMap['bar_code']] ?? '', '" '),
                        'cost_excl' => floatval($row[$columnMap['cost_excluding']] ?? 0),
                        'cost_incl' => floatval($row[$columnMap['cost_including']] ?? 0),
                        'rsp_excl' => floatval($row[$columnMap['rsp_excluding']] ?? 0),
                        'rsp_incl' => floatval($row[$columnMap['rsp_including']] ?? 0),
                    ]);

                    $processed++;
                    
                    // Show progress every 50 records
                    if ($processed % 50 === 0) {
                        $this->command->info("Processed {$processed} devices...");
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->command->error("Error processing row: " . $e->getMessage());
                }
            }

            DB::commit();
            fclose($file);

            $this->command->info("✓ Successfully imported {$processed} devices.");
            if ($errors > 0) {
                $this->command->warn("⚠ Encountered {$errors} errors during import.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);
            $this->command->error("Import failed: " . $e->getMessage());
            throw $e;
        }
    }
}
