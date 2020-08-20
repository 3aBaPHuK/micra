<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 14.08.2020
 * Time: 10:46
 */

namespace app\module\BasicAuth;

interface UserInterface
{
    public function getLogin();

    public function getPassword();

    public function populate(array $data);
}