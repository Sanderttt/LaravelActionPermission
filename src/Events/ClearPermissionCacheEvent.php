<?php
/**
 * Created by PhpStorm.
 * User: Win10H64
 * Date: 2-1-2017
 * Time: 09:50
 */

namespace RobinVanDijk\LaravelActionPermission\Events;


use Illuminate\Queue\SerializesModels;

class ClearPermissionCacheEvent
{
    use SerializesModels;

    public $cache_key;

    public function __construct($cache_key)
    {
        $this->cache_key = $cache_key;
    }

}