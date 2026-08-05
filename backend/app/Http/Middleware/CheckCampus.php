<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCampus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Super admin bypass
        if ($user->email === config('auth.super_admin_email')) {
            return $next($request);
        }

        // Check if user has campus_id
        if (!isset($user->campus_id)) {
            return response()->json([
                'success' => false,
                'message' => 'No campus assigned to this user',
            ], 403);
        }

        // Set campus_id in request for easy access
        $request->merge(['campus_id' => $user->campus_id]);

        return $next($request);
    }
}
