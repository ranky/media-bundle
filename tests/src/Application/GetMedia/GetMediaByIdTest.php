<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\GetMedia;

use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\GetMedia\GetMediaById;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class GetMediaByIdTest extends BaseUnitTestCase
{


    public function testItShouldGetMediaById(): void
    {
        $media     = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $uploadUrl = '/upload';

        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $mediaRepository
            ->expects($this->once())
            ->method('getById')
            ->with($media->id())
            ->willReturn($media);

        $responseTransformer = $this->getMediaTransformer($media->createdBy(), $uploadUrl);

        $showMediaResponse = (new GetMediaById(
            $mediaRepository,
            $responseTransformer,
        ))->__invoke($media->id()->asString());

        $this->assertEquals(
            MediaResponse::fromMedia(
                $media,
                $uploadUrl,
                $media->createdBy()->value(),
                $media->createdBy()->value()
            ),
            $showMediaResponse
        );
    }


}
