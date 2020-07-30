<?php

namespace app;

/**
 * Created by PhpStorm.
 * User: applebred
 * Date: 11.07.18
 * Time: 10:32
 */

use app\controller\Controller;

class Router
{

    public static function start()
    {
        $baseController = new Controller();
        $template       = $baseController->getConfigParam('template');

        try {
            $route          = self::getRoute();
            $controllerName = ucfirst($route['controller']?? '');

            $namespace = $controllerName ? "app\\controller\\{$controllerName}" : "app\\controller\\Controller" ;

            if ($template) {
                if (class_exists("app\\template\\$template\\controller\\{$controllerName}", true)) {
                    $namespace = "app\\template\\$template\\controller\\{$controllerName}";
                }
            }

            $controllerNamespace = $namespace;
            $action              = isset($route['action']) ? $route['action'] : 'index';
            $controller          = new $controllerNamespace();

            $controller->$action();
        } catch (\Error $error) {
            http_response_code('404');
            $baseController->renderNotFound($error);
        }
    }

    /**
     * @param $segments
     *
     * @return array
     */
    public static function parse(&$segments)
    {

        $url = parse_url($segments);
        if (isset($url['query'])) {
            $query = $url['query'];
        }

        if ($url['path']) {
            $path       = explode('/', $url['path']);
            $controller = $path[1] ?? '';
            $action     = $path[2] ?? '';
        }

        if (!empty($query) || (isset($controller) && isset($action))) {
            $params = [];
            if (!empty($query)) {
                $values = explode('&', $query);
                foreach ($values as $key => $value) {
                    if ($value === '') {
                        unset($values[$key]);
                    }
                    $parameter = explode('=', $value);

                    if (!empty($parameter)) {
                        $parameters[$parameter[0]] = isset($parameter[1]) ? $parameter[1] : '';
                    }

                }
                reset($values);

                foreach ($values as $value) {
                    $key = explode('=', $value);

                    if (isset($key[0]) && isset($key[1])) {

                        $params[$key[0]] = $key[1];

                    }

                }
            }

            return [
                'controller' => isset($controller) ? $controller : (isset($parameters['controller']) ? $parameters['controller'] : ''),
                'action'     => isset($action) ? $action : (isset($parameters['action']) ? $parameters['action'] : ''),
                'params'     => $params
            ];
        }

        return [];
    }

    /**
     * @return array
     */
    public static function getRoute()
    {
        $route = self::parse($_SERVER['REQUEST_URI']);

        $cRoute = [];

        if (isset($route['controller']) && $route['controller'] !== '') {
            $action               = isset($route['action']) ? $route['action'] : 'index';
            $cRoute['controller'] = $route['controller'];
            $cRoute['action']     = $action;
            $cRoute['params']     = $route['params'];
        }

        return $cRoute;
    }
}