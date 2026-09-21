<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage in routes: middleware('role:admin')
     *                  middleware('role:admin,staff')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return $this->deny($request, 'Unauthenticated.');
        }

        if (!in_array($request->user()->role, $roles)) {
            return $this->deny($request, 'Unauthorized. Insufficient permissions.');
        }

        if ($request->user()->status !== 'active') {
            return $this->deny($request, 'Your account is inactive. Contact the administrator.');
        }

        return $next($request);
    }

    private function deny(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        abort(403, $message);
    }
}
