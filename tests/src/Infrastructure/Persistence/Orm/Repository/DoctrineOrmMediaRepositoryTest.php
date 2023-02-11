<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Repository;

use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Criteria\MediaCriteria;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Exception\NotFoundMediaException;
use Ranky\MediaBundle\Domain\ValueObject\Description;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMediaRepository;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;
use Ranky\SharedBundle\Filter\ConditionOperator;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;
use Ranky\SharedBundle\Filter\ConditionFilter;

final class DoctrineOrmMediaRepositoryTest extends BaseIntegrationTestCase
{
    private MediaRepository $mediaRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaRepository = $this->getService(DoctrineOrmMediaRepository::class);
    }

    public function testItShouldSaveMedia(): MediaId
    {
        $media = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $this->mediaRepository->save($media);
        $this->assertSame($media, $this->mediaRepository->getById($media->id()));

        return $media->id();
    }

    /**
     * @depends testItShouldSaveMedia
     */
    public function testItShouldUpdateMedia(MediaId $mediaId): MediaId
    {
        $description = new Description('This is an alt', 'This is a title');
        $media       = $this->mediaRepository->getById($mediaId);
        $media->changeDescription($description, UserIdentifier::fromString('pedro'));
        $this->mediaRepository->save($media);
        $this->assertSame($media, $this->mediaRepository->getById($media->id()));

        return $media->id();
    }

    /**
     * @depends testItShouldSaveMedia
     */
    public function testItShouldGiveSizeMedia(): void
    {
        $this->assertSame(1, $this->mediaRepository->size());
    }

    /**
     * @depends testItShouldSaveMedia
     */
    public function testItShouldFilterMedia(MediaId $mediaId): MediaId
    {
        $filterById       = new ConditionFilter('id', ConditionOperator::EQUALS, $mediaId->asString());
        $offsetPagination = new OffsetPagination(1, 10);
        $orderBy          = new OrderBy('id', OrderBy::DESC);

        $medias       = $this->mediaRepository->filter(new MediaCriteria([$filterById], $offsetPagination, $orderBy));
        $currentMedia = $this->mediaRepository->getById($mediaId);
        $this->assertContains($currentMedia, $medias);

        return $mediaId;
    }

    /**
     * @depends testItShouldUpdateMedia
     */
    public function testItShouldFindMediaByFileName(MediaId $mediaId): MediaId
    {
        $media = $this->mediaRepository->getById($mediaId);
        $this->assertSame($media, $this->mediaRepository->getByFileName($media->file()->name()));

        return $media->id();
    }

    /**
     * @depends testItShouldFindMediaByFileName
     */
    public function testItShouldDeleteMedia(MediaId $mediaId): void
    {
        $this->mediaRepository->delete($this->mediaRepository->getById($mediaId));
        $this->expectExceptionObject(NotFoundMediaException::withId($mediaId->asString()));
        $this->mediaRepository->getById($mediaId);
    }


}
