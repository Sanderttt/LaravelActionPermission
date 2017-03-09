<?php
/**
 * Created by PhpStorm.
 * User: Win10H64
 * Date: 4-11-2016
 * Time: 15:39
 */

namespace RobinVanDijk\LaravelActionPermission\Contracts;

interface ActionManagerContract
{
    function massSync($records);

    function listActions();

    function ignoreActions($actions);

    function listIgnoredActions();

    function setNavActions($actions);

    function listNavActions();

    function getActionByMethodAndPath($method, $path);

    function verify($method, $path, $user, $host);

    function updateGroupAndAlias($groups, $aliases);
}
