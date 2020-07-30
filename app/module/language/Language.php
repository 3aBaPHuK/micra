<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 14.04.2020
 * Time: 16:49
 */

namespace app\module\language;

use app\module\ModuleInterface;

class Language implements ModuleInterface
{

    /**
     * @var string
     */
    public $currentLanguage;

    /**
     * @var array
     */
    private $templateData;

    public function installModule($params = null): ModuleInterface
    {
        $this->currentLanguage = $params['language'];
        $this->templateData    = $this->getTemplateFile();

        return $this;
    }

    /**
     * @param       $name
     * @param mixed ...$args
     *
     * @return string
     */
    public function trans($name, ...$args) {
        if (empty($this->templateData[$name])) {

            return $name;
        }

        return sprintf($this->templateData[$name], ...$args);
    }

    private function getTemplateFile()
    {
        $str_data = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->currentLanguage . '.json');

        return json_decode($str_data, true);
    }
}