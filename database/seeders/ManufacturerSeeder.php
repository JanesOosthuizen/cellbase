<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manufacturers = [
            'NOKIA',
            'SAMSUNG',
            'SONY',
            'LG',
            'VODAFONE',
            'APPLE',
            'HUAWEI',
            'ALCATEL',
            'BLACKBERRY',
            'CATTERPILLAR',
            'LENOVO',
            'VODACOM MOBIWIRE',
            'ZTE',
            'XIAOMI',
            'MOBILE CONNECT',
            'TABLETS',
            'LAPTOPS',
            'ACCESSORIES',
            'SIM Cards',
            'ProCell',
            'HISENSE',
            'MOBICELL',
            'VODACOM',
            'WATCH WEARABLES',
            'HONOR',
            'TABLETS & LAPTOPS',
            'OTHER',
            'TECNO',
            'VIVO',
            'MARA',
            'CROSSCALL',
            'MOBICEL',
            'Oppo',
            'HTC',
            'CATERPILLAR',
            'MICROSOFT',
            'Digital Planet',
            'V-CIoT',
            'Zworx',
            'TCL',
            'Transcend',
            'Fitbit',
            'Beats',
            'SIMCARDS',
            'ITEL',
            'NOTHING',
            'APPLE (GOOD AS NEW)',
            'MODEMS',
            'Rugged SA',
            'ACER',
            'ASUS',
            'Burtone',
            'Celcom',
            'Cellsell',
            'Cernotech',
            'DEO',
            'Gammatek',
            'HMD',
            'Hp',
            'Infinity data',
            'Instacom',
            'MECER',
            'Msi',
            'Nintendo',
            'Nokia-Lucent',
            'Nology',
            'Playstation',
            'Proline',
            'QVWI',
            'Realme',
            'SMD',
            'SOS Mobile',
            'Spectra',
            'Xbox',
        ];

        // Remove duplicates and create manufacturers
        $uniqueManufacturers = array_unique($manufacturers);
        
        foreach ($uniqueManufacturers as $name) {
            Manufacturer::firstOrCreate(
                ['name' => trim($name)],
                ['name' => trim($name)]
            );
        }
    }
}
