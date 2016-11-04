<?php
/**
 * Created by PhpStorm.
 * User: Win10H64
 * Date: 4-11-2016
 * Time: 13:44
 */

namespace RobinVanDijk\LaravelActionPermission;

use ReflectionClass;

class ControllerParser
{
    const CONTROLLER_METHOD_SEPARATOR = '@';

    const CONTROLLER_NAMESPACE_SEPARATOR = '\\';

    const NAME_REPLACEMENT = '';

    public function extractControllerInfo($action, $controller = [])
    {

        $controller['path'] = $action;

        $controller = array_merge($this->explodeControllerName($action), $controller);

        $controller_reflection = $this->createReflectionClass($controller['controller']);

        $controller['name'] = $this->retrieveName($controller_reflection, $controller['function']);

        return $controller;
    }

    protected function explodeControllerName($controller)
    {
        $parts = explode(self::CONTROLLER_METHOD_SEPARATOR, $controller);

        $controller = $parts[0];
        $function = $parts[1];

        return compact('controller', 'function');
    }

    protected function createReflectionClass($controller_name)
    {
        if (substr($controller_name, 0, 1) != '\\') {
            $controller_name = '\\' . $controller_name;
        }

        return new ReflectionClass($controller_name);
    }

    protected function retrieveName($controller_reflection, $function_name)
    {
        $name = $controller_reflection->getShortName();

        if (config('action-permission.controller_name_parts_to_be_removed') > 0 &&
            $parts_to_be_removed = config('action-permission.controller_name_parts_to_be_removed')
        ) {
            $name = $this->removePartsOfControllerName($parts_to_be_removed, $name);
        }

        if (!$name) {
            $namespaces = $this->explodeControllerNameSpace($controller_reflection->getNamespaceName());
            $name = snake_case($this->useLastNameSpaceAsName($namespaces));
        } else {
            $name = snake_case($name);
        }

        return trim(config('action-permission.translation_prefix'),
            '.') . '.' . $name . '.' . snake_case($function_name);
    }

    protected function removePartsOfControllerName($parts_to_be_removed, $name)
    {

        return str_replace($parts_to_be_removed, self::NAME_REPLACEMENT, $name);
    }

    protected function explodeControllerNameSpace($controller)
    {

        return explode(self::CONTROLLER_NAMESPACE_SEPARATOR, $controller);
    }

    protected function useLastNameSpaceAsName($namespaces)
    {

        return count($namespaces) > 0 ? array_slice($namespaces, -1)[0] : null;
    }
}
