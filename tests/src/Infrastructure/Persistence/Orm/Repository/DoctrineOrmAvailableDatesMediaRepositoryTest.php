<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Repository;

use Ranky\MediaBundle\Domain\Contract\AvailableDatesMediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmAvailableDatesMediaRepository;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMediaRepository;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

final class DoctrineOrmAvailableDatesMediaRepositoryTest extends BaseIntegrationTestCase
{
    private AvailableDatesMediaRepositoryInterface $availableDatesMediaRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->availableDatesMediaRepository = $this->getService(DoctrineOrmAvailableDatesMediaRepository::class);
    }

    public static function setUpBeforeClass(): void
    {
        $media = MediaFactory::random(
            MimeType::IMAGE,
            'jpg',
            null,
            UserIdentifier::fromString('jcarlos')
        );
        self::service(DoctrineOrmMediaRepository::class)->save($media);
    }

    public function testItShouldGetAnArrayWithAllAvailableDates(): void
    {
        $availableDates = $this->availableDatesMediaRepository->getAll();
        $this->assertContainsEquals([
            'year'  => (int)\date('Y'),
            'month' => (int)\date('m'),
            'count' => 1,
        ], $availableDates);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::resetTableByClassName(Media::class);
    }
}
