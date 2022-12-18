<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\GetMedia;

use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\GetMedia\GetMediaByFileName;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class GetMediaByFileNameTest extends BaseUnitTestCase
{


    public function testItShouldGetMediaByFileName(): void
    {
        $media     = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $uploadUrl = '/upload';

        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $mediaRepository
            ->expects($this->once())
            ->method('getByFileName')
            ->with($media->file()->name())
            ->willReturn($media);

        $responseTransformer = $this->getMediaTransformer($media->createdBy(), $uploadUrl);

        $showMediaResponse = (new GetMediaByFileName(
            $mediaRepository,
            $responseTransformer,
        ))->__invoke($media->file()->name());

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
