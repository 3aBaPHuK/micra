<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 14.04.2020
 * Time: 13:55
 */
namespace app\module;

interface ModuleInterface
{
    public function installModule($params = null):ModuleInterface;
}