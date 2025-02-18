<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::withoutEvents(function () {
            User::create([
                'email' => 'account_1@newProject.com',
                'phone' => '0' . (1152840068),
                'tenant_id' => 1,
                'tenant_db_id' => 1
            ]);

            // User::create([
            //     'email' => 'moderator@' . env('CENTRAL_DOMAIN'),
            //     'phone' => '0' . (1252840068),
            //     'tenant_id' => 1,
            //     'tenant_db_id' => 2
            // ]);
        });
    }
}
