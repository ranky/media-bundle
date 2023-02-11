<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\FileManipulation;

use Ranky\MediaBundle\Application\FileManipulation\GenerateThumbnails\GenerateImageThumbnails;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Contract\TemporaryFileRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\Service\FileResizeHandler;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\MediaBundle\Tests\Domain\ThumbnailsFactory;

class GenerateImageThumbnailsTest extends BaseUnitTestCase
{
    public function testItShouldGenerateImageThumbnails(): void
    {
        $media            = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $file             = $media->file();
        $thumbnails       = ThumbnailsFactory::make($media);
        $thumbnailArray   = $thumbnails->toArray();
        $originalMaxWidth = Media::ORIGINAL_IMAGE_MAX_WIDTH;

        /* MediaRepository */
        $mediaRepository = $this->createMock(MediaRepository::class);

        /* FileResizeHandler */
        $temporaryOriginalUrl          = $this->getTemporaryDirectory($file->path());
        $temporaryThumbnailUrlCallback = fn (string $breakpoint, string $fileName) => $this->getTemporaryDirectory(
            '/'.$breakpoint.'/'.$fileName
        );

        $consecutiveParameters = array_reduce(
            $thumbnailArray,
            static function ($thumbnails, $thumbnail) use (
                $file,
                $temporaryOriginalUrl,
                $temporaryThumbnailUrlCallback
            ) {
                $thumbnails[] = [
                    $file,
                    $temporaryOriginalUrl,
                    $temporaryThumbnailUrlCallback($thumbnail['breakpoint'], $file->path()),
                    new Dimension($thumbnail['width'], $thumbnail['height']),
                ];

                return $thumbnails;
            },
            []
        );

        $countCallResizeHandler = \count($thumbnailArray);
        if ($media->dimension()->width() > $originalMaxWidth) {
            array_unshift($consecutiveParameters, [
                $file,
                $this->getTemporaryDirectory($file->name()),
                $this->getTemporaryDirectory($file->name()),
                new Dimension($originalMaxWidth),
            ]);
            $countCallResizeHandler++;
        }

        $fileResizeHandler = $this->createMock(FileResizeHandler::class);
        $fileResizeHandler
            ->method('support')
            ->willReturn(true);

        $fileResizeHandler
            ->expects($this->exactly($countCallResizeHandler))
            ->method('resize')
            ->withConsecutive(...$consecutiveParameters)
            ->willReturn(true);


        /* TemporaryFileRepository */
        $countCallTemporaryRepository                = \count($thumbnailArray);
        $consecutiveParametersForTemporaryRepository = array_reduce(
            $thumbnailArray,
            static function ($thumbnails, $thumbnail) {
                $thumbnails[] = ['/'.$thumbnail['breakpoint'].'/'.$thumbnail['name']];

                return $thumbnails;
            },
            []
        );

        $consecutiveReturnForTemporaryRepository = \array_reduce(
            $thumbnailArray,
            static function ($thumbnails, $thumbnail) use ($temporaryThumbnailUrlCallback) {
                $thumbnails[] = $temporaryThumbnailUrlCallback(
                    $thumbnail['breakpoint'],
                    $thumbnail['name']
                );

                return $thumbnails;
            },
            []
        );
        /* For first call in generateThumbnails */
        array_unshift(
            $consecutiveParametersForTemporaryRepository,
            [$file->path()]
        );
        array_unshift(
            $consecutiveReturnForTemporaryRepository,
            $this->getTemporaryDirectory($file->path())
        );

        if ($media->dimension()->width() > $originalMaxWidth) {
            array_unshift($consecutiveParametersForTemporaryRepository, [$file->path()]);
            array_unshift($consecutiveReturnForTemporaryRepository, [$this->getTemporaryDirectory($file->path())]);
            $countCallTemporaryRepository++;
        }

        $temporaryFileRepository = $this->createMock(TemporaryFileRepository::class);
        $temporaryFileRepository
            ->expects($this->exactly($countCallTemporaryRepository + 1))
            ->method('temporaryFile')
            ->withConsecutive(...$consecutiveParametersForTemporaryRepository)
            ->willReturnOnConsecutiveCalls(...$consecutiveReturnForTemporaryRepository);

        $generateImageThumbnails = new GenerateImageThumbnails(
            $originalMaxWidth,
            $this->getBreakpoints(),
            $fileResizeHandler,
            $mediaRepository,
            $temporaryFileRepository,
        );


        $generateImageThumbnails->generate(
            $media->id()->asString(),
            $file,
            $media->dimension()
        );
    }
}
