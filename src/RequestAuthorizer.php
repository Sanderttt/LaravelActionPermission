<?php

namespace RobinVanDijk\LaravelActionPermission;

use Illuminate\Http\Request;
use RobinVanDijk\LaravelActionPermission\Contracts\ActionManagerContract;

class RequestAuthorizer
{
    public function __construct(ActionManagerContract $action_manager)
    {
        $this->action_manager = $action_manager;
    }

    public function verify(Request $request)
    {
        $current_method = $request->method();
        $current_action = $request->route()->getAction();

        if (!array_key_exists('controller', $current_action)) {
            return true;
        }

        return true;
    }
}
