<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\FileManipulation\Resize;

use Psr\Log\NullLogger;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Enum\GifResizeDriver;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\ImagickGifFileResize;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class ImagickGifFileResizeTest extends BaseIntegrationTestCase
{
    use FileResizeAssertTrait;

    public function testItShouldResizeGifImageWithImagickDriver(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('The imagick extension is not available.');
        }

        $fileGifResizeService = new ImagickGifFileResize(GifResizeDriver::IMAGICK->value, new NullLogger());
        $media                = MediaFactory::random(MimeType::IMAGE, 'gif');

        $this->assertAndResizeFile($fileGifResizeService, $media->file(), Breakpoint::XSMALL);
    }

}
