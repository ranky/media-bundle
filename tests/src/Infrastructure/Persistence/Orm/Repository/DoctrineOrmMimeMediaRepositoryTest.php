<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Repository;

use Ranky\MediaBundle\Domain\Contract\MimeMediaRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMediaRepository;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMimeMediaRepository;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

final class DoctrineOrmMimeMediaRepositoryTest extends BaseIntegrationTestCase
{
    private MimeMediaRepository $mimeMediaRepository;
    private const MIME_TYPE    = MimeType::IMAGE;
    private const MIME_SUBTYPE = 'gif';

    protected function setUp(): void
    {
        parent::setUp();
        $this->mimeMediaRepository = $this->getService(DoctrineOrmMimeMediaRepository::class);
    }

    public static function setUpBeforeClass(): void
    {
        $media = MediaFactory::random(
            self::MIME_TYPE,
            self::MIME_SUBTYPE,
            null,
            UserIdentifier::fromString('jcarlos')
        );
        self::service(DoctrineOrmMediaRepository::class)->save($media);
    }

    public function testItShouldGetAnArrayWithAllMime(): void
    {
        $mimes = $this->mimeMediaRepository->getAll();

        $this->assertContains([
            'mime'  => self::MIME_TYPE->value.'/'.self::MIME_SUBTYPE,
            'count' => 1,
        ], $mimes);
    }

    public function testItShouldGetAnArrayWithAllMimeGroupByType(): void
    {
        $mimesGroupByType = $this->mimeMediaRepository->getAllByType();

        $this->assertContains([
            'mimeType' => self::MIME_TYPE->value,
            'count'    => 1,
        ], $mimesGroupByType);
    }

    public function testItShouldGetAnArrayWithAllMimeGroupBySubType(): void
    {
        $mimesGroupBySubType = $this->mimeMediaRepository->getAllBySubType();

        $this->assertContains([
            'mimeSubType' => self::MIME_SUBTYPE,
            'count'       => 1,
        ], $mimesGroupBySubType);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::resetTableByClassName(Media::class);
    }

}
