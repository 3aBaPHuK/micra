<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 14.08.2020
 * Time: 9:55
 */

namespace app\module\BasicAuth;

use app\module\ModuleInterface;

class BasicAuth implements ModuleInterface
{

    /**
     * @var AuthProviderInterface
     */
    private $provider;

    /**
     * @var UserInterface | null
     */
    private $user = null;


    public function getCurrentUser()
    {

        return $this->user;
    }

    public function isAuthorize()
    {
        if (!$result = $this->provider->authorize($this->user)) {
            header('WWW-Authenticate: Basic realm="Micra.Framework"');
            header('HTTP/1.0 401 Unauthorized');

            $this->user = null;

            return false;
        }

        if (is_array($result)) {
            $this->user->populate($result);
        }

        return $result;
    }

    public function installModule($params = null): ModuleInterface
    {
        if ($params['provider']) {
            $this->provider = new $params['provider']();
        }

        if ($params['userEntity']) {
            $this->user = new $params['userEntity']();
        }

        return $this;
    }
}