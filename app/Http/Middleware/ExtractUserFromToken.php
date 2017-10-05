<?php

namespace Thoughts\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Extract user from jwt token and log him in.
 *
 * @package Sabichona\Http\Middleware
 */
class ExtractUserFromToken
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {

            $user = JWTAuth::parseToken()->authenticate();

            Auth::login($user);

        } catch (Exception $exception) {

            //do nothing

        }

        return $next($request);

    }

}
