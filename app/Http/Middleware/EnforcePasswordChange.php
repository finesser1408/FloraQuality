<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforcePasswordChange
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->require_password_change) {
                // Allow profile edit, update and logout requests
                if (!$request->routeIs('profile.edit') && 
                    !$request->routeIs('profile.update') && 
                    !$request->routeIs('logout')) {
                    
                    return redirect()->route('profile.edit')
                        ->with('error', 'For security, you must change your default password before accessing other parts of the system.');
                }
            }
        }

        return $next($request);
    }
}
