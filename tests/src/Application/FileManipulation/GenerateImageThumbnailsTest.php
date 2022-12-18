<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\FileManipulation;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\FileManipulation\Thumbnails\GenerateThumbnails\GenerateImageThumbnails;
use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Contract\FileUrlResolverInterface;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\Service\FileResizeHandler;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalFileRepository;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\MediaBundle\Tests\Domain\ThumbnailsFactory;

class GenerateImageThumbnailsTest extends TestCase
{
    public function testItShouldGenerateImageThumbnails(): void
    {
        $media           = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $file            = $media->file();
        $thumbnails      = ThumbnailsFactory::make($media);
        $thumbnailArray = $thumbnails->toArray();

        $originalMaxWidth     = Media::ORIGINAL_IMAGE_MAX_WIDTH;
        $breakpoints          = array_reduce(Breakpoint::cases(), static function ($breakpoints, $breakpoint) {
            $breakpoints[$breakpoint->value] = $breakpoint->dimensions();

            return $breakpoints;
        }, []);

        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $mediaRepository
            ->expects($this->once())
            ->method('getById')
            ->with($media->id())
            ->willReturn($media);
        $mediaRepository
            ->expects($this->once())
            ->method('save')
            ->with($media);

        $filePathResolver = $this->createMock(FilePathResolverInterface::class);
        $filePathResolver
            ->method('resolve')
            ->with($file->name())
            ->willReturn(sys_get_temp_dir().'/ranky_media_bundle_test/uploads/'.$file->name());

        $consecutiveParameters = array_reduce(
            $thumbnailArray,
            static function ($thumbnails, $thumbnail) {
                $thumbnails[] = [$thumbnail['breakpoint'], $thumbnail['name']];

                return $thumbnails;
            },
            []
        );
        $consecutiveReturn     = array_reduce(
            $thumbnailArray,
            static function ($thumbnails, $thumbnail) {
                $thumbnails[] = sys_get_temp_dir(
                    ).'/ranky_media_bundle_test/uploads/'.$thumbnail['breakpoint'].'/'.$thumbnail['name'];

                return $thumbnails;
            },
            []
        );
        $filePathResolver
            ->expects($this->exactly(count($thumbnailArray)))
            ->method('resolveFromBreakpoint')
            ->withConsecutive(...$consecutiveParameters)
            ->willReturnOnConsecutiveCalls(...$consecutiveReturn);

        $consecutiveParameters = array_reduce(
            $thumbnailArray,
            static function ($thumbnails, $thumbnail) use ($file) {
                $thumbnails[] = [
                    $file,
                    sys_get_temp_dir().'/ranky_media_bundle_test/uploads/'.$file->name(),
                    sys_get_temp_dir().'/ranky_media_bundle_test/uploads/'.$thumbnail['breakpoint'].'/'.$file->name(),
                    new Dimension($thumbnail['width'], $thumbnail['height']),
                ];

                return $thumbnails;
            },
            []
        );

        $countResize = \count($thumbnailArray);
        if ($media->dimension()->width() > $originalMaxWidth){
            array_unshift($consecutiveParameters, [
                $file,
                sys_get_temp_dir().'/ranky_media_bundle_test/uploads/'.$file->name(),
                sys_get_temp_dir().'/ranky_media_bundle_test/uploads/'.$file->name(),
                new Dimension($originalMaxWidth),
            ]);
            $countResize++;
        }


        $fileResizeHandler = $this->createMock(FileResizeHandler::class);
        $fileResizeHandler
            ->method('support')
            ->willReturn(true);

        $fileResizeHandler
            ->expects($this->exactly($countResize))
            ->method('resize')
            ->withConsecutive(...$consecutiveParameters)
            ->willReturn(true);

        $consecutiveParameters = array_reduce(
            $thumbnailArray,
            static function ($thumbnails, $thumbnail) {
                $thumbnails[] = [$thumbnail['breakpoint'], $thumbnail['name']];

                return $thumbnails;
            },
            []
        );

        $consecutiveReturn = array_reduce(
            $thumbnailArray,
            static function ($thumbnails, $thumbnail) {
                $thumbnails[] = '/'.$thumbnail['breakpoint'].'/'.$thumbnail['name'];

                return $thumbnails;
            },
            []
        );

        $fileUrlResolver = $this->createMock(FileUrlResolverInterface::class);
        $fileUrlResolver
            ->expects($this->exactly(\count($thumbnailArray)))
            ->method('resolvePathFromBreakpoint')
            ->withConsecutive(...$consecutiveParameters)
            ->willReturnOnConsecutiveCalls(...$consecutiveReturn);

        $fileRepository = $this->createMock(LocalFileRepository::class);

        $fileRepository
            ->method('filesizeFromPath')
            ->willReturn($media->file()->size());
        $fileRepository
            ->method('dimensionsFromPath')
            ->willReturn($media->dimension());

        $generateImageThumbnails = new GenerateImageThumbnails(
            $originalMaxWidth,
            $breakpoints,
            $fileResizeHandler,
            $mediaRepository,
            $fileRepository,
            $filePathResolver,
            $fileUrlResolver
        );
        $generateImageThumbnails->generate($media->id()->asString());
    }
}
