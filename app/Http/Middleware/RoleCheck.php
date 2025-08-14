<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // التأكد أن المستخدم مسجل دخول عبر Sanctum
        if (!auth('sanctum')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        $user = auth('sanctum')->user();

        // التحقق من الرول
        if ($user->role !== $role) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
