<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrMangerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager') return $next($request);
        else return response()->json(['error' => 'Доступ запрещен'], Response::HTTP_FORBIDDEN);
    }
}
