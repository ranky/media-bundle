<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\FileManipulation;

use Ranky\MediaBundle\Application\FileManipulation\CompressFile\CompressFile;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Service\FileCompressHandler;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class CompressFileTest extends BaseUnitTestCase
{
    public function testItShouldCompressFile(): void
    {
        /** Dummy data */
        $media = MediaFactory::random(MimeType::IMAGE, 'jpg');

        $mediaRepository = $this->createMock(MediaRepository::class);
        $mediaRepository
            ->expects($this->once())
            ->method('getById')
            ->with($media->id())
            ->willReturn($media);

        $compressHandler = $this->createMock(FileCompressHandler::class);
        $compressHandler
            ->expects($this->once())
            ->method('compress')
            ->with(
                $media->file(),
                $this->getTemporaryDirectory($media->file()->path())
            );

        $temporaryFileRepository = $this->getTemporaryFileRepository($media->file()->path());


        $compressFile = new CompressFile(
            false,
            false,
            $this->getBreakpoints(),
            $compressHandler,
            $mediaRepository,
            $temporaryFileRepository
        );
        $compressFile->__invoke($media->id()->asString());
    }
}
