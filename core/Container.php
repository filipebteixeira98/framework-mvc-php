<?php
namespace Core;

class Container
{
    public static function newController($controller)
    {
        $obj_controller = 'App\\Controllers\\' . $controller;
        return new $obj_controller;
    }
}
?>
