<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\GetMedia;

use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\GetMedia\GetMediaById;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class GetMediaByIdTest extends BaseUnitTestCase
{


    public function testItShouldGetMediaById(): void
    {
        $media     = MediaFactory::random(MimeType::IMAGE, 'jpg');

        $mediaRepository = $this->createMock(MediaRepository::class);
        $mediaRepository
            ->expects($this->once())
            ->method('getById')
            ->with($media->id())
            ->willReturn($media);

        $responseTransformer = $this->getMediaTransformer($media->createdBy());

        $showMediaResponse = (new GetMediaById(
            $mediaRepository,
            $responseTransformer,
        ))->__invoke($media->id()->asString());

        $this->assertEquals(
            MediaResponse::fromMedia(
                $media,
                $this->getUploadUrl(),
                $media->createdBy()->value(),
                $media->createdBy()->value()
            ),
            $showMediaResponse
        );
    }


}
