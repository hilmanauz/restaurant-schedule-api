<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $token = $request->header("Authorization");
        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return response()->json(
                [
                    "errors" => [
                        "message" => ["token not provided or invalid"]
                    ]
                ]
            )->setStatusCode(401);
        }
        $token = substr($token, 7); // Hapus "Bearer "
        $user = User::where("token", $token)->first();

        if (!$user) {
            return response()->json(
                [
                    "errors" => [
                        "message" => ["unauthorized"]
                    ]
                ]
            )->setStatusCode(401);
        } else if (!in_array($user->role, $roles)) {
            return response()->json(
                [
                    "errors" => [
                        "message" => ["forbidden"]
                    ]
                ]
            )->setStatusCode(403);
        } else {
            Auth::login($user);
        }

        return $next($request);
        ;
    }
}
