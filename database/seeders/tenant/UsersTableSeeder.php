<?php

namespace Database\Seeders\Tenant;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(App::isLocal()){
            User::create([
                'name' => 'admin',
                'email' => 'info@' . tenant()->main_domain,
                'phone' => '0' . (1152840068 + tenant()->id - 1),
                'password' => bcrypt('admin'),
            ])->syncRoles(['super admin']);
        }


        // User::create([
        //     'name' => 'moderator',
        //     'email' => 'moderator@' . tenant()->main_domain,
        //     'phone' => '0' . (1252840068 + tenant()->id - 1),
        //     'password' => bcrypt('moderator'),
        //     'register_id' => 2,
        //     'pin_code' => '111111'
        // ])->syncRoles(['moderator']);
    }
}
