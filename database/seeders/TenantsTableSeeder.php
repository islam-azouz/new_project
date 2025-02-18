<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Database\Seeders\Tenant\TenantsDatabasesSeeder;
use Illuminate\Database\Seeder;

class TenantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ([
            'account_1'
        ] as $tenant) {
            ($createdTenant = Tenant::create([
                'name' => $tenant,
                'main_domain' => $tenant . '.' . env('CENTRAL_DOMAIN', 'localhost')
            ]))->domains()->create([
                'domain' => $tenant
            ]);

            $createdTenant->run(function () {
                app(TenantsDatabasesSeeder::class)->run();
            });
        }
    }
}
