<?php
/**
 * Created by PhpStorm.
 * User: Win10H64
 * Date: 4-11-2016
 * Time: 12:47
 */

namespace RobinVanDijk\LaravelActionPermission\Contracts;

interface RouterManagerContract
{
    function getRoutes();

    function getRouter();

    function getActionsFromRoutes($routes);
}
