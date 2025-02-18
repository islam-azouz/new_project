<?php

namespace Database\Seeders\Tenant;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Artisan::call('cache:clear');

        Role::create(['name' => 'super admin', 'is_super_admin' => true])->givePermissionTo(Permission::all());

        Role::create(['name' => 'moderator'])->givePermissionTo(Permission::all());
    }
}
