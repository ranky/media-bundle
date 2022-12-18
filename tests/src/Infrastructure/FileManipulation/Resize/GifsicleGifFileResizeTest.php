<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\FileManipulation\Resize;

use Psr\Log\NullLogger;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Enum\GifResizeDriver;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Thumbnails\Resize\GifsicleGifFileResize;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class GifsicleGifFileResizeTest extends BaseIntegrationTestCase
{
    use FileResizeAssertTrait;

    public function testItShouldResizeGifImageWithGifsicle(): void
    {
        $gifsicleCanBeUsed = (bool)@shell_exec('gifsicle --version');
        if (!$gifsicleCanBeUsed) {
            $this->markTestSkipped('The gifsicle program is not available.');
        }

        $fileGifResizeService = new GifsicleGifFileResize(GifResizeDriver::GIFSICLE->value, new NullLogger());
        $media                = MediaFactory::random(MimeType::IMAGE, 'gif');

        $this->assertAndResizeFile($fileGifResizeService, $media->file(), Breakpoint::XSMALL);
    }

}
