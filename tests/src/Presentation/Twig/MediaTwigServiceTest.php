<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Presentation\Twig;


use Doctrine\Common\Collections\ArrayCollection;
use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\FindMedia\FindMediaByIds;
use Ranky\MediaBundle\Application\FindMedia\FindMediaByPaths;
use Ranky\MediaBundle\Application\GetMedia\GetMediaByFilePath;
use Ranky\MediaBundle\Application\GetMedia\GetMediaById;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Presentation\Twig\MediaTwigService;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

class MediaTwigServiceTest extends BaseUnitTestCase
{
    private Media $media;
    private UserIdentifier $userIdentifier;
    private MediaToResponseTransformer $responseTransformer;
    private MediaTwigService $mediaTwigService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->media               = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $this->userIdentifier      = UserIdentifier::fromString('jcarlos');
        $this->responseTransformer = $this->getMediaTransformer($this->userIdentifier, '/uploads');
    }

    public function testItShouldGetMediaByIdAndConvertMediaToResponse(): void
    {
        $getMediaByPath   = $this->createMock(GetMediaByFilePath::class);
        $findMediaByPaths = $this->createMock(FindMediaByPaths::class);
        $getMediaById     = $this->createMock(GetMediaById::class);
        $findMediaByIds   = $this->createMock(FindMediaByIds::class);

        $this->mediaTwigService = new MediaTwigService(
            $getMediaByPath,
            $findMediaByPaths,
            $getMediaById,
            $findMediaByIds,
            $this->responseTransformer
        );
        $mediaResponse          = $this->mediaTwigService->mediaToResponse($this->media);
        $getMediaById
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->media->id()->asString())
            ->willReturn($mediaResponse);


        $this->assertSame(
            $mediaResponse,
            $this->mediaTwigService->findById((string)$this->media->id())
        );
    }

    public function testItShouldFindMediaByIdsAndConvertMediaToArrayResponse(): void
    {
        $getMediaByPath   = $this->createMock(GetMediaByFilePath::class);
        $findMediaByPaths = $this->createMock(FindMediaByPaths::class);
        $getMediaById     = $this->createMock(GetMediaById::class);
        $findMediaByIds   = $this->createMock(FindMediaByIds::class);

        $this->mediaTwigService = new MediaTwigService(
            $getMediaByPath,
            $findMediaByPaths,
            $getMediaById,
            $findMediaByIds,
            $this->responseTransformer
        );
        $arrayCollection        = new ArrayCollection();
        $arrayCollection->add($this->media);
        $mediaResponse = $this->mediaTwigService->mediaCollectionToArrayResponse($arrayCollection);
        $findMediaByIds
            ->expects($this->once())
            ->method('__invoke')
            ->with([$this->media->id()->asString()])
            ->willReturn($mediaResponse);


        $this->assertSame(
            $mediaResponse,
            $this->mediaTwigService->findByIds([$this->media->id()->asString()])
        );
    }
}
