<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 14.04.2020
 * Time: 14:15
 */

namespace app\traits;

use app\module\ModuleInterface;
use ReflectionClass;

trait ModuleTrait
{

    final function installCustomModule($name, $resultName = '', $params = null)
    {
        $modules = $this->getModuleClasses();
        /**
         * @var ModuleInterface $module
         */

        if (!empty($modules[$name])) {
            $moduleName = $modules[$name];
            $module     = new $moduleName();

            $this->{$resultName} = $module->installModule($params);
        }
    }

    public function getModuleClasses()
    {
        $moduleDirs = array_filter(scandir(ROOTPATH . DIRECTORY_SEPARATOR . 'module'), function ($item) {
            return !in_array($item, ['.', '..', 'ModuleInterface.php']);
        });

        $classes = get_declared_classes();

        foreach ($moduleDirs as $dir) {
            $namespace = 'app\\module\\' . $dir . '\\' . ucfirst($dir);

            if (empty($classes[$namespace])) {
                class_exists('app\\module\\' . $dir . '\\' . ucfirst($dir));
            }
        }

        $classes = get_declared_classes();

        $moduleClasses = [];
        foreach ($classes as $class) {
            try {
                $reflection = new ReflectionClass($class);

                if ($reflection->implementsInterface('app\\module\\ModuleInterface')) {
                    $moduleClasses[$reflection->name] = $class;
                }
            } catch (\ReflectionException $e) {
            }
        }

        return $moduleClasses;
    }
}