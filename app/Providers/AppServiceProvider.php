<?php

namespace App\Providers;

use App\Models\BranchIp;
use App\Models\Settings;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\ServiceProvider;
use Bavix\Wallet\WalletConfigure;
use Carbon\Carbon;
use App\Models\Employee;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        app()->singleton('sharedData', function () {

            $neededSettings = Settings::whereIn('type', ['general_settings'])->get()->keyBy('type');

            $data = (object) [];


            return $data;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('FORCE_HTTPS', false)) {
            \URL::forceScheme('https');
        }
    }
}
