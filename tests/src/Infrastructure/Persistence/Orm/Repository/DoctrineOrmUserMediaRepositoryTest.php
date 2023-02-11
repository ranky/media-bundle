<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Repository;


use Ranky\MediaBundle\Domain\Contract\UserMediaRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMediaRepository;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmUserMediaRepository;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;


final class DoctrineOrmUserMediaRepositoryTest extends BaseIntegrationTestCase
{

    private UserMediaRepository $userMediaRepository;
    private static UserIdentifier $userIdentifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userMediaRepository = $this->getService(DoctrineOrmUserMediaRepository::class);
    }

    public static function setUpBeforeClass(): void
    {
        self::$userIdentifier = UserIdentifier::fromString('jcarlos');
        $media                = MediaFactory::random(
            MimeType::IMAGE,
            'gif',
            null,
            self::$userIdentifier
        );
        self::service(DoctrineOrmMediaRepository::class)->save($media);
    }


    public function testItShouldGetUsernameByUserIdentifier(): void
    {
        $username = $this->userMediaRepository->getUsernameByUserIdentifier(self::$userIdentifier);
        $this->assertSame(self::$userIdentifier->value(), $username);
    }


    public function testItShouldGetAnArrayWithAllMediaUser(): void
    {
        $mediaUsers = $this->userMediaRepository->getAll();

        $this->assertEquals([
            'username'   => self::$userIdentifier,
            'count'      => 1,
        ], $mediaUsers[0]);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::resetTableByClassName(Media::class);
    }
}
