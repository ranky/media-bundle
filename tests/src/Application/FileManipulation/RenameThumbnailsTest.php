<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\FileManipulation;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\FileManipulation\Thumbnails\RenameThumbnails\RenameThumbnails;
use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\FileUrlResolverInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\MediaBundle\Tests\Domain\ThumbnailsFactory;

class RenameThumbnailsTest extends TestCase
{
    public function testItShouldRenameThumbnails(): void
    {
        $media           = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $thumbnails      = ThumbnailsFactory::make($media);
        $thumbnailsArray = $thumbnails->toArray();
        $newFileName     = 'rename.'.$media->file()->extension();
        $siteUrl         = $_ENV['SITE_URL'];


        $consecutiveParameters = array_reduce(
            $thumbnailsArray,
            static function ($thumbnails, $thumbnail) use ($newFileName) {
                $thumbnails[] = [$thumbnail['breakpoint'], $thumbnail['name']];
                $thumbnails[] = [$thumbnail['breakpoint'], $newFileName];

                return $thumbnails;
            },
            []
        );
        $consecutiveReturn = array_reduce(
            $thumbnailsArray,
            static function ($thumbnails, $thumbnail) use ($newFileName) {
                $thumbnails[] = sys_get_temp_dir(
                    ).'/ranky_media_bundle_test/uploads/'.$thumbnail['breakpoint'].'/'.$thumbnail['name'];
                $thumbnails[] = sys_get_temp_dir(
                    ).'/ranky_media_bundle_test/uploads/'.$thumbnail['breakpoint'].'/'.$newFileName;

                return $thumbnails;
            },
            []
        );


        $filePathResolver = $this->createMock(FilePathResolverInterface::class);
        $filePathResolver
            ->expects($this->exactly(\count($consecutiveParameters)))
            ->method('resolveFromBreakpoint')
            ->withConsecutive(...$consecutiveParameters)
            ->willReturnOnConsecutiveCalls(...$consecutiveReturn);


        $consecutiveParameters = array_reduce(
            $thumbnailsArray,
            static function ($thumbnails, $thumbnail) use ($newFileName) {
                $thumbnails[] = [
                    sys_get_temp_dir(
                    ).'/ranky_media_bundle_test/uploads/'.$thumbnail['breakpoint'].'/'.$thumbnail['name'],
                    sys_get_temp_dir().'/ranky_media_bundle_test/uploads/'.$thumbnail['breakpoint'].'/'.$newFileName,
                ];

                return $thumbnails;
            },
            []
        );
        $fileRepository        = $this->createMock(FileRepositoryInterface::class);
        $fileRepository
            ->expects($this->exactly(\count($thumbnailsArray)))
            ->method('rename')
            ->withConsecutive(...$consecutiveParameters);

        $consecutiveParameters = array_reduce(
            $thumbnailsArray,
            static function ($thumbnails, $thumbnail) use ($newFileName) {
                $thumbnails[] = [$thumbnail['breakpoint'], $newFileName];

                return $thumbnails;
            },
            []
        );

        $consecutiveReturn = array_reduce(
            $thumbnailsArray,
            static function ($thumbnails, $thumbnail) use ($newFileName) {
                $thumbnails[] = '/'.$thumbnail['breakpoint'].'/'.$newFileName;

                return $thumbnails;
            },
            []
        );

        $fileUrlResolver = $this->createMock(FileUrlResolverInterface::class);
        $fileUrlResolver
            ->expects($this->exactly(\count($thumbnailsArray)))
            ->method('resolvePathFromBreakpoint')
            ->withConsecutive(...$consecutiveParameters)
            ->willReturnOnConsecutiveCalls(...$consecutiveReturn);

        $renameThumbnails = new RenameThumbnails($fileRepository, $fileUrlResolver, $filePathResolver);
        $renameThumbnails->__invoke($thumbnails, $newFileName);
    }
}
