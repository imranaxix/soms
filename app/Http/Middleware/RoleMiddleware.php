<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            if ($request->user() && $request->user()->role === 'manufacturer') {
                return redirect()->route('manufacturer.dashboard')->with('error', 'Unauthorized access.');
            }
            if ($request->user() && $request->user()->role === 'shop_owner') {
                return redirect()->route('shop.dashboard')->with('error', 'Unauthorized access.');
            }
            
            return redirect('/login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
