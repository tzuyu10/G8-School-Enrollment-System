<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role->code, $roles)) {
            if (!$request->expectsJson()) {
                return redirect()->route('unauthorized');
            }

            return response()->json([
                'message' => 'Unauthorized. You do not have permission to access this resource.',
            ], 403);
        }

        if ($user->status?->code === 'inactive') {
            if (!$request->expectsJson()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('account.inactive');
            }

            return response()->json([
                'message' => 'Account inactive.',
            ], 403);
        }

        return $next($request);
    }
}
