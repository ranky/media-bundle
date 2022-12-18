<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\FileManipulation;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\FileManipulation\CompressFile\CompressFile;
use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Service\FileCompressHandler;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class CompressFileTest extends TestCase
{
    public function testItShouldCompressFile(): void
    {
        /** Dummy data */
        $media = MediaFactory::random(MimeType::IMAGE, 'jpg');

        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $mediaRepository
            ->expects($this->once())
            ->method('getById')
            ->with($media->id())
            ->willReturn($media);


        $fileRepository = $this->createMock(FileRepositoryInterface::class);
        $fileRepository
            ->method('filesizeFromPath');

        $filePathResolver = $this->createMock(FilePathResolverInterface::class);
        $filePathResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($media->file()->path())
            ->willReturn(sys_get_temp_dir().'/ranky_media_bundle_test/upload/'.$media->file()->path());

        $compressHandler = $this->createMock(FileCompressHandler::class);
        $compressHandler
            ->expects($this->once())
            ->method('compress')
            ->with(sys_get_temp_dir().'/ranky_media_bundle_test/upload/'.$media->file()->path(), $media->file());


        $compressFile = new CompressFile(false, false, $compressHandler, $mediaRepository, $fileRepository, $filePathResolver);
        $compressFile->__invoke($media->id()->asString());
    }
}
