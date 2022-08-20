<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class ApiAuthentication
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        $user = User::where('token', $token)->first();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'Unauthenticated'
                ], 403
            );
        }


        return $next($request);
    }
}
