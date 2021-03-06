<?php

namespace RobinVanDijk\LaravelActionPermission;

use RobinVanDijk\LaravelActionPermission\Contracts\ActionManagerContract;
use RobinVanDijk\LaravelActionPermission\Entities\Action;

class ActionManager implements ActionManagerContract
{
    protected $action;

    protected $cache;

    public function __construct()
    {
        $this->action = app()->make(Action::class);
        $this->cache = app()->make('Illuminate\Contracts\Cache\Repository');
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

    /**
     * @param $groups
     * @param $aliases
     * @return bool
     */
    function updateGroupAndAlias($groups, $aliases)
    {
        foreach ($groups as $key => $value) {
            $this->action->where('id', '=', $key)->update(['group' => $value]);
        }

        foreach ($aliases as $key => $value) {
            $this->action->where('id', '=', $key)->update(['alias' => $value]);
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

    function listNavActions($user = null)
    {
        $user_id = null;
        if ($user) {
            $user_id = $user->id;
        }

        if (cache()->tags([config('action-permission.cache_key')])->has('nav' . $user_id)) {
            return cache()->tags([config('action-permission.cache_key')])->get('nav' . $user_id);
        }
        if ($user) {
            $user_actions = $user->actions()->withoutGlobalScopes()->where('in_nav', '=', 1)->get();
            $actions = collect($user_actions);
            foreach ($user->roles()->get() as $role) {
                foreach ($role->actions()->withoutGlobalScopes()->where('in_nav', '=',
                    1)->get()->all() as $role_action) {
                    $actions->push($role_action);
                }
            }
            $unique_actions = $actions->sortBy('group')->unique();

            $actions = $unique_actions->values()->all();
        } else {
            $actions = $this->action->withoutGlobalScopes()->where('in_nav', '=', 1)->get()->toArray();
        }

        cache()->tags([config('action-permission.cache_key')])->put('nav' . $user_id, $actions, 60 * 60 * 24 * 7);

        return $actions;
    }

    function getActionByMethodAndPath($method, $path)
    {
        return $this->action->where('method', '=', $method)->where('path', '=', $path)->first();
    }

    function verify($method, $path, $user, $host)
    {
        if (in_array($host, config()->get('action-permission.excluded_domains'))) {
            return true;
        }

        $action = $this->getCurrentRouteAction($method, $path);
        if (!isset($action) || !$action || $action['is_ignored']) {
            return true;
        }

        $allowed_actions = $this->getUserActions($user);

        if (!in_array($action['id'], $allowed_actions)) {
            return false;
        }

        return true;
    }

    public function getCurrentRouteAction($method, $path)
    {
        $action = $this->getAction($method, $path);

        return $action;
    }

    public function getAction($method, $path)
    {
        if ($this->cache->tags(config('action-permission.cache_key'))->has($method . '.' . $path)) {
            return $this->cache->tags(config('action-permission.cache_key'))->get($method . '.' . $path);
        }

        $action = $this->action->where('method', '=', $method)->where('path', '=', $path)->first();
        $action = $action ? $action->toArray() : [];
        $this->cache->tags(config('action-permission.cache_key'))->put($method . '.' . $path, $action,
            60 * 60 * 24 * 7);

        return $action;
    }

    public function getUserActions($user)
    {
        if (!isset($user)) {
            return [];
        }

        if ($this->cache->tags([config('action-permission.cache_key'), $user->id])->has('actions')) {
            return $this->cache->tags([config('action-permission.cache_key'), $user->id])->get('actions');
        }

        $user_actions = $user->actions()->withoutGlobalScopes()->get()->pluck('id')->toArray();
        $role_actions = [];
        foreach ($user->roles()->withoutGlobalScopes()->get() as $role) {
            $role_actions[] = $role->actions->pluck('id')->toArray();
        }

        $allowed_actions = array_unique(array_merge($user_actions, array_flatten($role_actions)));
        $this->cache->tags([config('action-permission.cache_key'), $user->id])->put('.actions', $allowed_actions,
            60 * 60 * 24 * 7);

        return $allowed_actions;
    }
}
