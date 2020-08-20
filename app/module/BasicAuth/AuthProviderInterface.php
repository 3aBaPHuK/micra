<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 14.08.2020
 * Time: 10:43
 */

namespace app\module\BasicAuth;

interface AuthProviderInterface
{
    public function authorize(UserInterface $user);

    public function getUser(): UserInterface;
}