<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\FindMedia;

use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\FindMedia\FindMediaByIds;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class FindMediaByIdsTest extends BaseUnitTestCase
{

    public function testItShouldFindMediaByIds(): void
    {
        $media     = MediaFactory::random(MimeType::IMAGE, 'jpg');

        $mediaRepository = $this->createMock(MediaRepository::class);
        $mediaRepository
            ->expects($this->once())
            ->method('findByIds')
            ->with(...[$media->id()])
            ->willReturn([$media]);

        $responseTransformer = $this->getMediaTransformer($media->createdBy());
        $findMediaResponse = new FindMediaByIds(
            $mediaRepository,
            $responseTransformer,
        );

        $this->assertEquals(
            [MediaResponse::fromMedia(
                $media,
                $this->getUploadUrl(),
                $media->createdBy()->value(),
                $media->createdBy()->value()
            )],
            $findMediaResponse->__invoke([$media->id()->asString()])
        );
    }


}
