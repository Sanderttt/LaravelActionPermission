<?php

namespace RobinVanDijk\LaravelActionPermission\Listeners;

use RobinVanDijk\LaravelActionPermission\Events\ClearPermissionCacheEvent;


/**
 * @property  \Illuminate\Contracts\Cache\Repository cache
 */
class ClearPermissionCacheListener
{

    public function __construct()
    {
        $this->cache = app()->make('Illuminate\Contracts\Cache\Repository');
    }

    public function handle(ClearPermissionCacheEvent $event)
    {
        $this->cache->forget($event->cache_key);
    }
}