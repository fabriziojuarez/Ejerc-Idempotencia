<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header('Idempotency-Key');
        
        if (!$idempotencyKey) {
            return response()->json([
                'success' => false,
                'message' => 'Idempotency-Key es requerida.',
            ], 400);
        }

        if(Cache::has($idempotencyKey)) {
            return response()->json([
                'success' => true,
                'message' => 'Pago recuperado del cache',
                'data' => Cache::get($idempotencyKey),
                'replayed' => true,
            ]);
        }

        $response = $next($request);

        $body = json_decode($response->getContent(), true);
        Cache::put($idempotencyKey, $body['data'], now()->addHours(24));
        
        return $response;
    }
}
