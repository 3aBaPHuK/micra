<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 20.08.2020
 * Time: 13:30
 */

namespace app\provider;

use app\model\User;
use app\module\BasicAuth\AuthProviderInterface;
use app\module\BasicAuth\UserInterface;
use app\traits\ConfigTrait;

class ConfigAuthProvider implements AuthProviderInterface
{

    use ConfigTrait;

    private $config;

    /**
     * @var UserInterface
     */
    private $user;

    public function __construct()
    {
        $this->config = $this->createConfig();
    }

    public function authorize(UserInterface $user)
    {
        if (!$user) {
            return false;
        }

        if (!$configAuthData = $this->getConfigParam('authorizationData')) {
            return false;
        }

        if ($user->getLogin() === $configAuthData['login'] && $user->getPassword() === $configAuthData['password']) {

            return $user;
        }

        return false;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}