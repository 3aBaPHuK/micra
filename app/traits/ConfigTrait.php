<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 14.08.2020
 * Time: 11:03
 */

namespace app\traits;

trait ConfigTrait
{
    /**
     * @param string $name
     * @param string $section
     *
     * @return mixed
     */
    public function getConfigParam(string $name, string $section = '')
    {
        if (empty($this->config[$name]) && empty($this->config[$section][$name])) {
            return false;
        }

        return $section ? $this->config[$section][$name] : $this->config[$name];
    }

    /**
     * @return array
     */
    private function createConfig()
    {
        return json_decode(file_get_contents(ROOTPATH . '/config.json'), true);
    }
}