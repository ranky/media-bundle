<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\FileManipulation\Resize;

use Psr\Log\NullLogger;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Enum\ImageResizeDriver;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\InterventionFileResize;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class InterventionFileResizeTest extends BaseIntegrationTestCase
{
    use FileResizeAssertTrait;

    public function testItShouldResizeJpgImageWithImagickDriver(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('The imagick extension is not available.');
        }
        $fileResizeService = new InterventionFileResize(ImageResizeDriver::IMAGICK->value, new NullLogger());
        $media              = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $this->assertAndResizeFile($fileResizeService, $media->file(), Breakpoint::LARGE);
    }

    public function testItShouldResizeJpgImageWithGDDriver(): void
    {
        if (!\extension_loaded('gd') || !\extension_loaded('exif')) {
            $this->markTestSkipped('The gd or exif extension is not available.');
        }

        $media              = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $fileResizeService = new InterventionFileResize(ImageResizeDriver::GD->value, new NullLogger());
        $this->assertAndResizeFile($fileResizeService, $media->file(), Breakpoint::LARGE);
    }

}
