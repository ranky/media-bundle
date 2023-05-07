<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ranky\MediaBundle\Tests\Dummy\User\Domain\User;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('jcarlos');
        $user->setPassword('password');
        $user->setEmail('jcarlos@test.test');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('pedro');
        $user->setPassword('password');
        $user->setEmail('pedro@test.test');
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
