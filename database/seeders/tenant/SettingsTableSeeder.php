<?php

namespace Database\Seeders\Tenant;

use App\Models\Account;
use App\Models\Journal;
use App\Models\Settings;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::insert([
            [
                'type' => 'general_settings',
                'data' => '{
                    "city": "Nasr City",
                    "phone": "+01220343833",
                    "address": "Nasr City , Cairo , Egypt",
                    "country": "Saudi Arabia"
                }'
            ]
        ]);
    }
}
