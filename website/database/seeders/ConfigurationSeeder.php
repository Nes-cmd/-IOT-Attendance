<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuration;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Configuration::create([
            'user_id' => 1,
            'day' => json_encode(['monday' => 1,'tuesday' => 1, 'wednsday' => 1, 'thursday' => 1, 'friday' => 1, 'saturday' => 0, 'sunday' => 0]),
            'data' => 'barcode',
            'wifi' => json_encode(['ssid' => 'Name of wifi', 'password' => 'password']),
            'label1' => json_encode(['name' => 'Morning', 'start' => '8:00', 'end' => '9:00', 'penality' => 15 ]),
        ]);
    }
}
