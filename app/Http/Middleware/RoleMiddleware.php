<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{

    public function handle($request, Closure $next, $role, $permission = null)
    {
        if(!$request->user()->hasRole($role)) {
            return response()->json(["error" => "You can't be here, hehe :)"]);
        }
        if($permission !== null && !$request->user()->can($permission)) {
            return response()->json(["error" => "You can't do this, hehe :)"]);
        }
        return $next($request);

    }
}
