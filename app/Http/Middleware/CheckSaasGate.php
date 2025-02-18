<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSaasGate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        # Check If Sending App Key
        if ($request->header('saas-gate-key') == env("APP_KEY")) {
            return $next($request);
        } else {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => [__("You Don't Have Permession To Acsess This Gate")]], 422);
            } else {
                # Custom
                return response()->json(['errors' => [__("You Don't Have Permession To Acsess This Gate")]], 422);
            }
        }
        return $next($request);
    }
}
