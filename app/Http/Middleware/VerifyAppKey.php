<?php

namespace App\Http\Middleware;

use App\Models\App;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAppKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('x-app-key');

        if (App::where('app_key', $apiKey)->count() === 0) {
            return response()->json([
                'errors' => [
                    'message' => [
                        'invalid app key'
                    ]
                ]
            ])->setStatusCode(403);
        }
        return $next($request);
    }
}
