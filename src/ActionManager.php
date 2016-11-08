<?php

namespace RobinVanDijk\LaravelActionPermission;

use RobinVanDijk\LaravelActionPermission\Contracts\ActionManagerContract;
use RobinVanDijk\LaravelActionPermission\Entities\Action;

class ActionManager implements ActionManagerContract
{
    protected $action;

    public function __construct()
    {
        $this->action = app(Action::class);
    }

    public function massSync($records)
    {
        $time = date('Y-m-d H:i:s');

        $records = array_map(function ($record) use ($time) {
            $record['created_at'] = $time;
            $record['updated_at'] = $time;

            return $record;
        }, $records);

        (new MassDatabaseActions())->run($this->action, $records,
            ['controller', 'function', 'method', 'path', 'name', 'created_at', 'updated_at'],
            ['path', 'name', 'updated_at'], $time);

        $this->action->where('updated_at', '<', $time)->delete();
    }

    function listAllActions()
    {
        return $this->action->get();
    }

    function listActions()
    {
        return $this->action->where('is_ignored', '=', 0)->get();
    }

    function ignoreActions($actions)
    {
        $this->action->where('is_ignored', '=', 1)->update(['is_ignored' => 0]);
        if ($actions) {
            $this->action->whereIn('id', $actions)->update(['is_ignored' => 1]);
        }

        return true;
    }

    function listIgnoredActions()
    {
        return $this->action->where('is_ignored', '=', 1)->get();
    }

    function setNavActions($actions)
    {
        $this->action->where('in_nav', '=', 1)->update(['in_nav' => 0]);
        if ($actions) {
            $this->action->whereIn('id', $actions)->update(['in_nav' => 1]);
        }

        return true;
    }

    function listNavActions()
    {
        return $this->action->where('in_nav', '=', 1)->get();
    }

    function getActionByMethodAndPath($method, $path)
    {
        return $this->action->where('method', '=', $method)->where('path', '=', $path)->first();
    }

    function verify($method, $path, $user)
    {
        $action = $this->action->where('method', '=', $method)->where('path', '=', $path)->first();
        if (!$action || $action->is_ignored) {
            return true;
        }
        $user_actions = $user->actions()->withoutGlobalScopes()->get()->pluck('id')->toArray();
        $role_actions = [];
        foreach ($user->roles()->withoutGlobalScopes()->get() as $role) {
            $role_actions[] = $role->actions->pluck('id')->toArray();
        }

        $allowed_actions = array_unique(array_merge($user_actions, array_flatten($role_actions)));
        if (!in_array($action->id, $allowed_actions)) {
            return false;
        }

        return true;
    }
}
