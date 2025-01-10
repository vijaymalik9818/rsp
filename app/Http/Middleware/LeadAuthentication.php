<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LeadAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('authorization');

        if ($header != null) {
            $token = explode(" ", $header)[1];
            $user = auth()->guard('api')->user();

            if ($user == null) {
                return response()->json(['error' => 'Invalid or expired Token'], 401);
            }
            $request->merge(['authenticated_user' => $user]);
            return $next($request);
        } else {
            return response()->json(['error' => 'Authorization header missing'], 401);
        }
    }
}
