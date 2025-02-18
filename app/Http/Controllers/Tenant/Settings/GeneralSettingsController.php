<?php

namespace App\Http\Controllers\Tenant\Settings;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchIp;
use Illuminate\Support\Facades\Cookie;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        $this->authorize('read General Settings');

        $generalSettings = Settings::where('type', 'general_settings')->firstOrCreate(['type' => 'general_settings']);
        $timezones       = json_decode(file_get_contents(base_path('resources/timezones.json')), true);
        $currencies      = DB::table('currencies')->select('id', 'name')->get();
        $branches        = Branch::pluck('name', 'id');

        return view('tenant.settings.general.index', compact('generalSettings', 'timezones', 'currencies', 'branches'));
    }

    public function store(Request $request)
    {
        $this->authorize('edit General Settings');

        # Valdiation Rules
        $rules    = [];

        # Valdiation Messages
        $messages = [];

        # Return Validation
        $this->validate($request, $rules, $messages);

        $generalSettings = [
        ];

        # Save To Database
        $st = Settings::updateOrCreate(
            ['type' => 'general_settings'],
            [
                'data' => json_encode($generalSettings),
            ]
        );

        $request->branch_id && BranchIp::updateOrCreate(['ip_address' => request()->ip()], ['branch_id' => $request->branch_id]);
        $request->branch_id && Cookie::queue('currentBranchId', $request->branch_id);

        if ($request->company_logo_remove) {
            $st->clearMediaCollection('company_logo');
        }

        if ($request->hasFile('company_logo')) {
            $st->clearMediaCollection('company_logo');
            $st->addMediaFromRequest('company_logo')->toMediaCollection('company_logo');
        }

        return self::checkResponse(True, 'store');
    }
}
