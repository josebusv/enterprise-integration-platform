<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ValidateHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validar el header Authorization
        if (!$request->hasHeader('Authorization')) {
            return response()->json([
                'error' => 'Authorization header is required.'
            ], 401);
        }

        // Validar el header X-Request-Id
        if (!$request->hasHeader('X-Request-Id')) {
            return response()->json([
                'error' => 'X-Request-Id header is required.'
            ], 400);
        }

        // Agregar X-Request-Id a los logs
        Log::withContext([
            'X-Request-Id' => $request->header('X-Request-Id')
        ]);

        return $next($request);
    }
}
