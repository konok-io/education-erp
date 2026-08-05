<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AcademicSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAcademicSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentSession = AcademicSession::where('is_current', true)->first();

        if (!$currentSession) {
            return response()->json([
                'success' => false,
                'message' => 'No active academic session found. Please contact administrator.',
            ], 403);
        }

        // Set session_id in request for easy access
        $request->merge(['academic_session_id' => $currentSession->id]);

        return $next($request);
    }
}
