<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 13.04.2020
 * Time: 11:29
 */

namespace app\controller;

use app\module\BasicAuth\BasicAuth;
use app\module\breadcrumbs\Breadcrumbs;
use app\module\input\Input;
use app\module\language\Language;
use app\traits\FileTrait;
use app\traits\ModuleTrait;
use app\traits\TwigTrait;

/**
 * @property Breadcrumbs pathway
 * @property  Language   language
 * @property  Input      input
 * @property  BasicAuth  auth
 */
class Controller
{

    use TwigTrait;
    use ModuleTrait;
    use FileTrait;

    /**
     * @var string
     */
    public $title;

    /**
     * @var array
     */
    public $languages;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string|string
     */
    protected $message;

    public function index() {

        $this->render('index', 'show');
    }

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->config = $this->createConfig();

        $this->installCustomModule('app\module\menu\Menu', 'menu');
        $this->installCustomModule('app\module\breadcrumbs\Breadcrumbs', 'pathway');
        $this->installCustomModule('app\module\language\Language', 'language', ['language' => $this->getCurrentLanguage()]);
        $this->installCustomModule('app\module\input\Input', 'input');

        if ($authModule = $this->getConfigParam('authorization')) {
            $this->installCustomModule($authModule, 'auth', [
                'provider'   => 'app\provider\ConfigAuthProvider',
                'userEntity' => 'app\model\User'
            ]);

            if (!$this->auth->isAuthorize()) {
                $this->renderNotAllowed();
            }
        }

        $rawLanguages    = $this->getConfigParam('languages');
        $this->languages = $this->getLanguageArr($rawLanguages);
    }

    public function getCurrentLanguage()
    {
        if (!isset($_COOKIE['interface_language'])) {
            return $this->getConfigParam('defaultLanguage');
        }

        return $_COOKIE['interface_language'];
    }

    /**
     * @param string $message
     */
    public function renderNotFound(string $message)
    {
        $this->message = $message;

        $this->render('error', '404', (array)$this);
    }

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
     * @param string     $tpl
     * @param string     $view
     * @param array|null $vars
     */
    protected function render(string $tpl, string $view, array $vars = null)
    {
        $this->loadTwig($this->getConfigParam('template'), $tpl, $view . '.twig', $vars ?: (array)$this);
    }

    /**
     * @return array
     */
    private function createConfig()
    {
        return json_decode(file_get_contents(ROOTPATH . '/config.json'), true);
    }

    /**
     * @param $rawLanguages
     *
     * @return array
     */
    private function getLanguageArr($rawLanguages): array
    {
        return array_map(function ($language) {
            return [
                'name'  => $this->language->trans($language),
                'value' => $language
            ];
        }, $rawLanguages);
    }

    protected function setResponseHeaders(array $data) {
        foreach ($data as $name => $value) {
            $trimmedValue = trim(preg_replace('/\s+/', ' ', preg_replace('~[\r\n]~', '', $value)));

            header("{$name}: {$trimmedValue}");
        }
    }

    /**
     * @param $data
     */
    public function responseJson($data)
    {
        $this->setResponseHeaders(['Content-Type' => 'application/json']);
        echo json_encode($data);
        die();
    }

    public function renderNotAllowed()
    {

        $this->render('error', '401', (array)$this);
        die();
    }


    public function renderNotEnoughAccessGranted()
    {

        $this->render('error', '403', (array)$this);
        die();
    }

    public function renderInternalError(string $message)
    {

        $this->render('error', '500', ['message' => $message]);
    }


    protected function enqueueMessage($message, $messageType = 'success')
    {
        $this->setResponseHeaders([
            'X-messages'     => $message,
            'X-messages-type' => $messageType
        ]);
    }

    public function renderRedirect($controller, $action, $params = [], $message = null, $messageType = 'alert')
    {
        $location = $this->createUrl($controller, $action, null, $params);

        header('Location:' . $location);
        header('X-message:' . $message);
        header('X-message-type:' . $messageType);
        die();
    }
}