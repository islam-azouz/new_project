<?php

namespace Database\Seeders\Tenant;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PermissionsTableSeeder extends Seeder
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

        $defaultGuard = config('auth.defaults.guard');

        $permissions = collect(include(resource_path('permissions.php')))->map(function ($permissions, $model) use ($defaultGuard) {

            if (class_exists('App\\Models\\' . $model))
                Artisan::call("make:policy {$model}Policy --model=$model");

            return collect($permissions)->map(function ($action) use ($model, $defaultGuard) {
                return [
                    'name' => $action . ' ' . $model,
                    'guard_name' => $defaultGuard,
                    'module' => $model,
                ];
            });
        })->flatten(1)->toArray();

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }
    }
}
