<?php

namespace App\Http\Controllers\SaasGate;

use App\Models\Role;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\Tenant\TenantsDatabasesSeeder;

class SG_AccountsController extends Controller
{

    public function createAccount(Request $request)
    {
        # Valdiation Rules
        $rules    = [
            'tenant_id' => 'required|numeric|unique:tenants,id',
            'sub_domain' => 'nullable|unique:domains,domain',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'required|numeric|unique:users,phone',
            'password'  => 'required|min:6',
        ];

        # Valdiation Messages
        $messages = [];

        if ($request->sub_domain) {
            $subDomain = $request->sub_domain;
        } else {
            $subDomain = 'Account_' . $request->tenant_id;
        }

        # Return Validation json
        $this->validate($request, $rules, $messages);

        $tenantId = $request->tenant_id;
        $tenant = Tenant::create([
            'id'          => $tenantId,
            'name'        => $subDomain,
            'main_domain' => $subDomain . '.' . env('CENTRAL_DOMAIN', 'localhost')
        ])
            ->domains()->create([
                'domain' => $subDomain
            ]);

        $tenant = Tenant::find($tenantId);


        $this->runTenantSeeders($tenant, $request);

        # insert into tenant database
        return response()->json(['success' => 'Data is successfully created', 'data' => []]);
    }

    public function runTenantSeeders($tenant, $request)
    {
        $tenant->run(function () use ($request) {
            app(TenantsDatabasesSeeder::class)->run();

            User::create([
                'name'        => 'admin',
                'email'       => $request->email,
                'phone'       => $request->phone,
                'password'    => bcrypt($request->password),
                'register_id' => 1,
                'pin_code'    => '123123'
            ])->syncRoles(['super admin']);

            # update company name
            $generalSettings = DB::table('settings')->where('type', 'general_settings')->first();
            $generalSettings = json_decode($generalSettings->data);
            $generalSettings->company_name  = $request->name;
            $generalSettings->default_email = $request->email;
            $generalSettings->phone         = $request->phone;
            $generalSettings = json_encode($generalSettings);
            DB::table('settings')->where('type', 'general_settings')->update(['data' => $generalSettings]);
        });
    }

    public function updateAccount(Request $request)
    {

        # Valdiation Rules
        $rules    = [
            'tenant_id' => 'required|numeric',
            'sub_domain' => 'unique:domains,domain,' . $request->tenant_id . ',tenant_id',
            'email'     => 'required|email|unique:users,email,' . $request->tenant_id . ',tenant_id',
            'phone'     => 'required|numeric|unique:users,phone,' . $request->tenant_id . ',tenant_id',
            'password'  => 'required|min:6',
        ];

        # Valdiation Messages
        $messages = [];

        # Return Validation json
        $this->validate($request, $rules, $messages);

        $tenantId = $request->tenant_id;

        $tenant = Tenant::find($tenantId);

        DB::table('domains')->where('tenant_id', $tenantId)->where('domain', $request->old_sub_domain)->update([
            'domain' => $request->sub_domain
        ]);

        User::where('tenant_id', $tenantId)->where('email', $request->old_email)->update([
            'email'       => $request->email,
            'phone'       => $request->phone,
        ]);

        $tenant->run(function () use ($request) {
            User::where('email', $request->old_email)->update([
                'name'        => 'admin',
                'email'       => $request->email,
                'phone'       => $request->phone,
                'password'    => bcrypt($request->password),
            ]);
        });

        # insert into tenant database
        return response()->json(['success' => 'Data is successfully updated', 'data' => []]);
    }

    public function deleteAccount(Request $request)
    {
        # Valdiation Rules
        $rules    = [
            'tenant_id' => 'required|numeric',
        ];

        # Valdiation Messages
        $messages = [];

        # Return Validation json
        $this->validate($request, $rules, $messages);

        $tenantId = $request->tenant_id;

        # delete tenant
        $tenant = Tenant::find($tenantId);
        $tenant->delete();

        # insert into tenant database
        return response()->json(['success' => 'Data is successfully deleted', 'data' => []]);
    }

    public function updateAccountSubscription(Request $request)
    {
        # Valdiation Rules
        $rules    = [
            'tenant_id' => 'required|numeric',
            'subscription_id' => 'required|numeric'
        ];

        # Valdiation Messages
        $messages = [];

        # Return Validation json
        $this->validate($request, $rules, $messages);

        $tenantId = $request->tenant_id;

        $tenant = Tenant::find($tenantId);


        $tenant->run(function () use ($request) {
            DB::table('subscriptions')->where('id', $request->subscription_id)->update([
                'is_paid' => "1"
            ]);
        });

        # insert into tenant database
        return response()->json(['success' => 'Data is successfully updated', 'data' => []]);
    }

}
