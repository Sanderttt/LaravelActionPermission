<?php

namespace RobinVanDijk\LaravelActionPermission\Middlewares;

use Closure;

class PermissionMiddleware
{
    public function handle($request, Closure $next)
    {
        $next = $next($request);

        // TODO authorize request
        return $next;
    }
}
