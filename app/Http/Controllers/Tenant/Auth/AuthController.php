<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Sales\PosController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Features\UserImpersonation;

class AuthController extends Controller
{
    /**
     * @var String
     */
    public $status;

    /**
     * @var Collection
     */
    public $sessions;

    /**
     * impersonate the comming requester as the user defined in the central database table 'tenant_user_impersonation_tokens'
     * @see https://tenancyforlaravel.com/docs/v3/features/user-impersonation/
     * @var String
     */
    public function impersonate($token)
    {
        $response = UserImpersonation::makeResponse($token);

        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $this->authenticationResponse();
        }

        return $response;
    }

    public function loginPage()
    {

        return view('tenant.auth.login');
    }

    public function login()
    {
        $credentials = request()->validate([
            'email' => 'required',
            'password' => 'required'
        ], [
            'email.required' => 'Email/phone is required',
            'password.required' => 'Password is required'
        ]);

        if (auth()->attempt($credentials) || auth()->attempt(['phone' => $credentials['email'], 'password' => $credentials['password']])) {
            request()->session()->regenerate();

        return request()->ajax() ? ['status' => 'success', 'message' => 'Login successful'] : redirect()->intended(route('dashboard'));

        }

        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }





    public function finalLogin()
    {
        $this->logCurrentSession();

        return request()->ajax() ? ['status' => 'success', 'message' => 'Login successful'] : redirect()->intended(route('dashboard'));
    }

    public function logout()
    {

        auth()->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()->route('login');
    }



    public function resetPassword($token)
    {
        return view('tenant.auth.contact_reset_password', ['token' => $token]);
    }
}
