<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!$request->user() || !$request->user()->is_active) {
            return redirect()->route('login');
        }

        if ($role === 'admin' && !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($role === 'cashier' && !$request->user()->isCashier()) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
