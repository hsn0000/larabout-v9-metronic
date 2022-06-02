<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Ajax
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$request->ajax())
        {
            if(!session('session_uid')) 
            {
                $_provided = [
                    'status' => false,
                    'error' => 'Login failur'
                ];

                return response()->json($_provided);
            }

            $_provided = [
                'status' => false,
                'error' => 'Service Unavailable'
            ];

            return abour(403, 'Service Unavailable');
        }

        return $next($request);
    }
}
