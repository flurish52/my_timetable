<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            $isIncomplete = !$user->programme_id || !$user->level_id;

            $allowedRoutes = [
                'setup.index',
                'setup.store',
                'logout'
            ];

            if ($isIncomplete && !$request->routeIs($allowedRoutes)) {
                return redirect()->route('setup.index');
            }
        }

        return $next($request);
    }
}
