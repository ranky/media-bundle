<?php

declare(strict_types=1);

use Ranky\MediaBundle\Tests\Dummy\User\Domain\User;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $securityConfig): void {
    $securityConfig
        ->passwordHasher(User::class)
        ->algorithm('plaintext');

    $securityConfig
        ->provider('users')
        ->entity(['class' => User::class, 'property' => 'username']);

    $securityConfig
        ->firewall('main')
        ->lazy(true)
        //->provider('users_in_memory')
        //->httpBasic()
    ;
};
