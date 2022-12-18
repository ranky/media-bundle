<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\FileManipulation;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\FileManipulation\RenameFile\RenameFile;
use Ranky\MediaBundle\Application\FileManipulation\Thumbnails\RenameThumbnails\RenameThumbnails;
use Ranky\MediaBundle\Application\SafeFileName\SafeFileName;
use Ranky\MediaBundle\Application\UpdateMedia\UpdateMediaRequest;
use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\MediaBundle\Tests\Domain\ThumbnailsFactory;
use Ranky\SharedBundle\Domain\Event\InMemoryDomainEventPublisher;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

class RenameFileTest extends TestCase
{
    public function testItShouldRenameFile(): void
    {
        /** Dummy data */
        $media              = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $userIdentifier     = UserIdentifier::fromString('jcarlos');
        $oldFileName        = $media->file()->name();
        $newFileName        = 'rename.'.$media->file()->extension();
        $oldThumbnails      = ThumbnailsFactory::make($media);
        $newThumbnails      = ThumbnailsFactory::withNewFileName($media, $newFileName);
        $media->addThumbnails($oldThumbnails);

        $updateMediaRequest = new UpdateMediaRequest(
            $media->id()->asString(),
            $newFileName,
            $media->description()->alt(),
            $media->description()->title(),
        );

        $renameThumbnails = $this->createMock(RenameThumbnails::class);
        $renameThumbnails
            ->expects($this->once())
            ->method('__invoke')
            ->with($oldThumbnails, $newFileName)
            ->willReturn($newThumbnails);

        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $mediaRepository
            ->expects($this->once())
            ->method('getById')
            ->with($media->id())
            ->willReturn($media);

        $safeFileName = $this->createMock(SafeFileName::class);
        $safeFileName
            ->expects($this->once())
            ->method('__invoke')
            ->with($updateMediaRequest->name(), $media->file()->extension())
            ->willReturn($newFileName);

        $filePathResolver = $this->createMock(FilePathResolverInterface::class);
        $filePathResolver
            ->expects($this->exactly(2))
            ->method('resolve')
            ->withConsecutive([$oldFileName], [$newFileName])
            ->willReturnOnConsecutiveCalls(
                sys_get_temp_dir().'/ranky_media_bundle_test/upload/'.$oldFileName,
                sys_get_temp_dir().'/ranky_media_bundle_test/upload/'.$newFileName
            );

        $fileRepository = $this->createMock(FileRepositoryInterface::class);
        $fileRepository
            ->expects($this->once())
            ->method('rename')
            ->with(
                sys_get_temp_dir().'/ranky_media_bundle_test/upload/'.$oldFileName,
                sys_get_temp_dir().'/ranky_media_bundle_test/upload/'.$newFileName
            );

        $domainEventPublisher = new InMemoryDomainEventPublisher(
            new \ArrayIterator([])
        );

        $renameFile = new RenameFile(
            $renameThumbnails,
            $mediaRepository,
            $fileRepository,
            $safeFileName,
            $filePathResolver,
            $domainEventPublisher
        );

        $renameFile->__invoke($updateMediaRequest, $userIdentifier->value());
    }
}
