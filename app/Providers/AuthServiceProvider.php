<?php

namespace App\Providers;

use App\Models\PurchasesPriceListRule;
use App\Models\Role;
use Exception;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        // Note: you can't return false from Gate::before method if the user is granted the permission from spatie/permissions package it's either null or true which will work
        // I think after installing spatie/permissions package the @can blade directive checks first if the user is granted the permission
        // if yes it won't check further authorization filters like Gate::before or Policies
        // if no it's continue to check Gate::before method and if returned null it checks policies if there is a realted policy to action or return unauthorized at last;
        Gate::before(function ($user, $ability, $models) {
            if($user->roles->contains('is_super_admin', true)) {
                return true;
            }
            if($ability == 'have-sub') {
                return true;
            }
            if (isset($models[0])) {
                // return null to leave the authorization for the policy
                return null;
            }

            // in the case of using @can in gates style like "read User" for example, in this case the application won't use the corresponding policy class
            // because we didn't call the authorization function in the policy style which is @can('read', 'App\\Models\User') to let the app guess the policy class
            // from the second argument
            // here we reroute the authorization to the corresponding policy after guessing it ourselves
            if (isset(explode(' ', $ability)[1]) && class_exists('App\\Models\\' . ($model = explode(' ', $ability)[1]))) {

                $action = explode(' ', $ability)[0];
                #TODO: replace read with the actual action depending on the variable $ability
                return $user->can($action , 'App\\Models\\' . $model);
            }

           // return $user->roles->contains('is_super_admin', true) ? true : null;
        });
    }
}
