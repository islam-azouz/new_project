<?php

namespace App\Http\Controllers\Tenant;

use Exception;
use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\Settings;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use App\Models\PurchasesBill;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

    public function index()
    {
        return view('tenant.dashboard');
    }

}
