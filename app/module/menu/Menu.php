<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 14.04.2020
 * Time: 13:54
 */
namespace app\module\menu;

use app\module\ModuleInterface;

class Menu implements ModuleInterface
{
    /**
     * @param array | null $params
     *
     * @return ModuleInterface
     */
    public function installModule($params = null): ModuleInterface
    {
        return $this;
    }

    public function getMenu($name) {
        return $this->createItems($name);
    }

    private function createItems($name) {
        $data = $this->getMenuFile($name, 'module/menu');

        return $data['items'];
    }

    private function getMenuFile(string $name, string $customDirectory = null)
    {

        $str_data = file_get_contents($this->getFilePath($name, $customDirectory));

        return json_decode($str_data, true);
    }

    private function getFilePath($name, $customDirectory = null) {

        return ROOTPATH . ($customDirectory ? DIRECTORY_SEPARATOR . $customDirectory : '') . DIRECTORY_SEPARATOR . 'menus' . DIRECTORY_SEPARATOR . $name . '.json';
    }

}