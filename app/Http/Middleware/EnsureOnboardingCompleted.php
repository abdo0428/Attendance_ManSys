<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
            return $next($request);
        }

        if ($user && !$user->onboarded_at) {
            if ($request->routeIs('onboarding') || $request->routeIs('onboarding.store')) {
                return $next($request);
            }

            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
