<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 13.04.2020
 * Time: 12:47
 */

namespace app\traits;

trait TwigTrait
{
    public function loadTwig($template, $tpl = 'tmpl', $view = '', $vars = [])
    {

        $loader = new \Twig\Loader\FilesystemLoader(ROOTPATH . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
        $twig   = new \Twig\Environment($loader, [
//            'cache' => ROOTPATH . '/cache',
            'cache' => false,
        ]);

        $createUrl         = new \Twig\TwigFunction('createUrl', [$this, 'createUrl']);
        $filter            = new \Twig\TwigFunction('filter', [$this, 'filter']);
        $dump              = new \Twig\TwigFilter('dump', 'var_dump');
        $printR            = new \Twig\TwigFilter('print', 'print_r');
        $getNpmScripts     = new \Twig\TwigFunction('getNpmScripts', [$this, 'getNpmScripts']);
        $getCurrentVersion = new \Twig\TwigFunction('getCurrentVersion', [$this, 'getCurrentVersion']);
        $getLibraryVersion = new \Twig\TwigFunction('getLibraryVersion', [$this, 'getLibraryVersion']);


        $twig->addFunction($createUrl);
        $twig->addFunction($filter);
        $twig->addFunction($getNpmScripts);
        $twig->addFunction($getCurrentVersion);
        $twig->addFunction($getLibraryVersion);
        $twig->addFilter($dump);
        $twig->addFilter($printR);
        $twig->addFilter(new \Twig\TwigFilter('cast_to_array', function ($stdClassObject) {
            $response = [];
            if ($stdClassObject) {
                foreach ($stdClassObject as $key => $value) {
                    $response[$key] = $value;
                }

                return $response;
            }

            return [];
        }));


        if (!empty($this->language)) {
            $twig->addFilter(new \Twig\TwigFilter('t', function($name, ...$params) {
                return $this->language->trans($name, ...$params);
            }));
        }

        echo $twig->render(DIRECTORY_SEPARATOR . $tpl . '/' . $view, $vars);
    }

    public function createUrl($controller = null, $action = null, $format = null, array $params = [])
    {
        if (!$controller && !$action) {
            return '';
        }

        $paths = [];
        $query = [];

        if ($controller) {
            if (is_array($controller)) {
                $paths = array_merge($paths, $controller);
            } else {
                $paths['controller'] = lcfirst($controller);
            }
        }

        if ($action) {
            if (is_array($action)) {
                $paths = array_merge($paths, $action);
            } else {
                $paths['action'] = $action;
            }
        }

        if ($format) {
            if (is_array($format)) {
                $query = array_merge($query, $format);
            } else {
                $query['format'] = $query;
            }
        }

        foreach ($params as $key => $param) {

            if (is_array($params)) {
                continue;
            }

            $params[$key] = trim($param);
        }

        if ($params) {
            $query = array_merge($query, $params);
        }

        $baseUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];

        if ($paths) {
            $controller = $paths['controller'];
            $action     = $paths['action'];
            $query      = http_build_query($query);
            $url        = "{$baseUrl}/{$controller}/{$action}?{$query}";
        } else {
            $url = $baseUrl;
        }

        return urldecode($url);
    }

    public function getCurrentVersion() {
        $revisionNumber = exec('git rev-list --count HEAD');
        $branchName     = exec('git rev-parse --abbrev-ref HEAD');

        return $branchName && $revisionNumber ? $branchName . '.' . $revisionNumber : 'no-data';
    }

    public function getNpmScripts()
    {
        $scripts    = scandir(ROOTPATH . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $this->getConfigParam('template') . DIRECTORY_SEPARATOR . 'asset/js/dist');
        $scriptsStr = '';


        foreach ($scripts as $script) {
            if ($script !== '.' && $script !== '..' && strpos($script, '.js'))
                $scriptsStr .= '<script src="/app/template/' . $this->getConfigParam('template') . '/asset/js/dist/' . $script . '"></script>';
        }

        return $scriptsStr;
    }
}