<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\UserSecurity;

use Ranky\MediaBundle\Tests\Dummy\User\Domain\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class LoginTest extends WebTestCase
{

    public function testItLoggedIn(): void
    {
        static::ensureKernelShutdown();
        $client = static::createClient();

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);
        $testUser       = $userRepository->getByUsername('jcarlos');
        if (!$testUser){
            throw new \RuntimeException('User with username "jcarlos" not found');
        }
        $client->loginUser($testUser);

        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = self::getContainer()->get(TokenStorageInterface::class);
        $this->assertSame(
            $testUser->getUsername(),
            $tokenStorage->getToken()?->getUser()?->getUserIdentifier()
        );
        /** @var AuthorizationCheckerInterface $authChecker */
        $authChecker = self::getContainer()->get(AuthorizationCheckerInterface::class);
        $this->assertTrue($authChecker->isGranted('ROLE_USER'));
    }
}
