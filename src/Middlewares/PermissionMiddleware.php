<?php

namespace RobinVanDijk\LaravelActionPermission\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use RobinVanDijk\LaravelActionPermission\Contracts\ActionManagerContract;

class PermissionMiddleware
{
    public function handle($request, Closure $next)
    {
        $next = $next($request);
        $action_manager = app()->make(ActionManagerContract::class);
        $user = Auth::user();

        if ($request->route() !== null && !$action_manager->verify($request->getMethod(),
                $request->route()->getActionName(), $user)
        ) {
            if ($user) {
                abort(403, 'Access denied');
            }

            return response()->redirect()->to('/login');
        }

        return $next;
    }
}
