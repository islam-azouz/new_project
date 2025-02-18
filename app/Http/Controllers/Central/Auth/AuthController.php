<?php

namespace App\Http\Controllers\Central\Auth;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\AdminGateHelper;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use AdminGateHelper;

    public function signupPage()
    {
        return view('central.auth.sign_up');
    }

    public function confirmPage($accountId)
    {
        # Get Account Data
        $response = $this->getUnderConstructionAccountData($accountId);
        if ($response['code'] == 500) {
            return redirect('signup');
        }
        $accountPhone = $response['response']['account_data']['phone'];
        return view('central.auth.confirm_sign_up')->with(compact('accountPhone', 'accountId'));
    }

    public function signup(Request $request)
    {

        # Valdiation Rules
        $rules    = [
            'company_name'        => 'required',
            'sub_domain'          => 'required|unique:domains,domain|regex:/^[a-zA-Z][-a-zA-Z0-9]+$/|max:20|min:3|not_in:www,mail,ftp,admin,api,central,dev,dev2,dev3,dev4,dev5,dev6,dev7,dev8,dev9,dev10,dev11,dev12,dev13,dev14,dev15,dev16,dev17,dev18,dev19,dev20,dev21,dev22,dev23,dev24,dev25,dev26,dev27,dev28,dev29,dev30,dev31,dev32,dev33,dev34,dev35,dev36,dev37,dev38,dev39,dev40,dev41,dev42,dev43,dev44,dev45,dev46,dev47,dev48,dev49,dev50,dev51,dev52,dev53,dev54,dev55,dev56,dev57,dev58,dev59,dev60,dev61,dev62,dev63,dev64,dev65,dev66,dev67,dev68,dev69,dev70,dev71,dev72,dev73,dev74,dev75,dev76,dev77,dev78,dev79,dev80,dev81,dev82,dev83,dev84,dev85,dev86,dev87,dev88,dev89,dev90,dev91,dev92,dev93,dev94,dev95,dev96,dev97,dev98,dev99,dev100,dev101,dev102,dev103,dev104,dev105,dev106,dev107,dev108,dev109,dev110,dev111,dev112,dev113,dev114,dev115,dev116,dev117,dev118,dev119,dev120,dev121,dev122,dev123,dev124,dev125,dev126,dev127,dev128,dev129,dev130,dev131,dev132,dev133,dev134,dev135,dev136,dev137,dev138,dev139,dev140,dev141,dev142,dev143,dev144,dev145,dev146,dev147,dev148,dev149,dev150,dev151,dev152,dev153,dev154,dev155,dev156,dev157,dev158,dev159,dev160,dev161,dev162',
            'mobile_number'       => 'required',
            'mobile_country_code' => 'required',
            'full_mobile_number'  => 'required|unique:users,phone',
            'email'               => 'required|email|unique:users,email',
            'password'            => 'required|min:6',
            'confirm_password'    => 'required|min:6|same:password',
            'accept'              => 'required',
        ];

        # Valdiation Messages
        $messages = [];

        # Return Validation json
        $this->validate($request, $rules, $messages);

        $response = $this->createUnderConstructionAccount($request);
        if ($response['code'] == 500) {
            return response()->json(['errors' => [$response['response']]], 422);
        }

        # Get Account Id
        $accountId = $response['response']['account_id'];



        # Return Success json
        return response()->json(['success' => 'Data is successfully created', 'account_id' => $accountId]);
    }

    public function resendConfirmationCode(Request $request)
    {

        $response = $this->resendUnderConstructionAccountConfirmationCode($request->account_id);
        if ($response['code'] == 500) {
            return response()->json(['errors' => [$response['response']]], 422);
        }
        # Return Success json
        return response()->json(['success' => 'successfully resend confirmation Code', 'account_id' => $request->account_id]);
    }

    public function confirmAccount(Request $request)
    {

        $response = $this->confirmUnderConstructionAccount($request->account_id, $request->confirmation_code);
        if ($response['code'] == 500) {
            return response()->json(['errors' => [$response['response']]], 422);
        }

        # Get Account Id
        $accountId = $response['response']['account_id'];

        # Login to tenant id
        $tenant = Tenant::find($accountId);
        return $tenant->run(function () use ($request) {
            Auth::loginUsingId(1, $remember = true);
            request()->session()->regenerate();

            $redirectUrl = tenant_route(tenant()->main_domain, 'dashboard');

            $token = tenancy()->impersonate(tenant(), auth()->id(), $redirectUrl);

            return response()->json(['success' => 'successfully confirmed account', 'redirect' => tenant_route(tenant()->main_domain, 'impersonate', $token->token)]);
        });
    }

    public function loginPage()
    {
        return view('tenant.auth.login');
    }

    public function login()
    {

        $tenant = User::where('email', request('email'))
            ->orWhere('phone', request('email'))->first()?->tenant;

        if (!$tenant) {
            return response()->json(['status' => 'error', 'message' => 'There is no user with this email or phone number'], 401);
        }

        return $tenant->run(function () {
            $credentials = request()->validate([
                'email' => 'required',
                'password' => 'required'
            ], [
                'email.required' => 'Email/phone is required',
                'password.required' => 'Password is required'
            ]);

            if (auth()->attempt($credentials) || auth()->attempt(['phone' => $credentials['email'], 'password' => $credentials['password']])) {
                request()->session()->regenerate();

                $redirectUrl = tenant_route(tenant()->main_domain, 'dashboard');

                $token = tenancy()->impersonate(tenant(), auth()->id(), $redirectUrl);

                return ['status' => 'success', 'message' => 'Login successful', 'redirect' => tenant_route(tenant()->main_domain, 'impersonate', $token->token)];
            }

            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        });
    }

    public function checkSubDomain(Request $request){
        $subDomain = $request->sub_domain;
        $checkDomain = DB::table('domains')->where('domain', $subDomain)->first();
        if($checkDomain){
            return response()->json('This subdomain is already taken', 200);
        }
        return 'true';
    }
}
