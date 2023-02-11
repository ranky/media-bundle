<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\UpdateMedia;

use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\FileManipulation\RenameFile\RenameFile;
use Ranky\MediaBundle\Application\UpdateMedia\UpdateMedia;
use Ranky\MediaBundle\Application\UpdateMedia\UpdateMediaRequest;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Domain\Event\InMemoryDomainEventPublisher;

class UpdateMediaTest extends BaseUnitTestCase
{


    public function testItShouldUpdateMedia(): void
    {
        $media              = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $newFileName        = 'rename.'.$media->file()->extension();
        $updateMediaRequest = new UpdateMediaRequest(
            $media->id()->asString(),
            $newFileName,
            $media->description()->alt(),
            $media->description()->title(),
        );

        $file = $media->file()->changeName($newFileName, $newFileName);
        $media->changeFile($file, $media->updatedBy());
        $mediaRepository = $this->createMock(MediaRepository::class);
        $mediaRepository
            ->expects($this->once())
            ->method('getById')
            ->with($media->id())
            ->willReturn($media);

        $mediaRepository
            ->expects($this->once())
            ->method('save')
            ->with($media);

        $renameFile = $this->createMock(RenameFile::class);
        $renameFile
            ->expects($this->once())
            ->method('__invoke')
            ->with($updateMediaRequest, $media->updatedBy()->value());

        $domainEventPublisher = new InMemoryDomainEventPublisher(
            new \ArrayIterator([])
        );

        $responseTransformer = $this->getMediaTransformer($media->updatedBy());

        $updateMediaResponse = (new UpdateMedia(
            $mediaRepository,
            $responseTransformer,
            $renameFile,
            $domainEventPublisher
        ))->__invoke($updateMediaRequest, $media->updatedBy()->value());

        $this->assertEquals(
            MediaResponse::fromMedia(
                $media,
                $this->getUploadUrl(),
                $media->createdBy()->value(),
                $media->updatedBy()->value()
            ),
            $updateMediaResponse
        );

        $this->assertSame(
            $newFileName,
            $updateMediaResponse->file()->name()
        );
    }

}
