<?php
/**
 * Created by PhpStorm.
 * User: Win10H64
 * Date: 4-11-2016
 * Time: 11:28
 */

namespace RobinVanDijk\LaravelActionPermission;

use RobinVanDijk\LaravelActionPermission\Contracts\RouterManagerContract;

class RouterManager implements RouterManagerContract
{

    protected $router;

    public function __construct()
    {
        $this->router = app()->make('Illuminate\Contracts\Routing\Registrar');
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getRoutes()
    {
        $all_routes = $this->router->getRoutes()->getRoutesByMethod();

        $methods = config('action-permission.included_request_methods');

        if (count($methods) < 1 || (count($methods) == 1 && $methods[0] == '*')) {
            return $all_routes;
        }

        extract($all_routes);

        return compact(...$methods);
    }

    public function getActionsFromRoutes($route_methods)
    {
        $actions = [];
        foreach ($route_methods as $method => $routes) {
            foreach ($routes as $route) {
                $action = $route->getAction();

                if (!array_key_exists('controller', $action)) {
                    continue;
                }

                $actions[] = (new ControllerParser())
                    ->extractControllerInfo($action['controller'], ['method' => $method]);
            }
        }

        return $actions;
    }
}
