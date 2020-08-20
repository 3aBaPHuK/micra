<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 13.08.2020
 * Time: 11:23
 */

namespace app\model;

use app\module\BasicAuth\UserInterface;

class User implements UserInterface
{
    /**
     * @var string
     */
    public $login;

    /**
     * @return string
     */
    public function getLogin():? string
    {
        return $_SERVER['PHP_AUTH_USER'] ?? null;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $_SERVER['PHP_AUTH_USER'] = $login;
    }

    /**
     * @return string
     */
    public function getPassword():? string
    {
        return $_SERVER['PHP_AUTH_PW'] ?? null;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $_SERVER['PHP_AUTH_PW'] = $password;
    }

    public function populate(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}