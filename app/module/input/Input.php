<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 15.04.2020
 * Time: 14:25
 */

namespace app\module\input;

use app\module\ModuleInterface;

class Input implements ModuleInterface
{

    public function installModule($params = null): ModuleInterface
    {
        return $this;
    }

    public function getString($arg, $default = false)
    {
        $result = isset($_GET[$arg]) ? $_GET[$arg] : $default;

        return $result;
    }

    public function getFiles($name) {
        return !empty($_FILES[$name]) ? $_FILES[$name] : false;
    }

    public function getInt($arg, $default = false)
    {
        $result = (int)(!empty($_GET[$arg]) ? $_GET[$arg] : $default);

        return $result;
    }

    public function get($arg)
    {

        return !empty($_GET[$arg]) ? $_GET[$arg] : null;
    }

    public function getArray()
    {

        return (array)$_GET;
    }

    public function formData() {
        return (array) $_POST;
    }
}