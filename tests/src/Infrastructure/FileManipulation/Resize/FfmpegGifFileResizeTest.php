<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\FileManipulation\Resize;

use Psr\Log\NullLogger;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Enum\GifResizeDriver;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\FfmpegGifFileResize;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class FfmpegGifFileResizeTest extends BaseIntegrationTestCase
{

    use FileResizeAssertTrait;


    public function testItShouldResizeGifImageWithFfmpeg(): void
    {
        $ffmpegCanBeUsed = (bool)@shell_exec('ffmpeg -version');
        if (!$ffmpegCanBeUsed) {
            $this->markTestSkipped('The ffmpeg program is not available.');
        }

        $fileGifResizeService = new FfmpegGifFileResize(GifResizeDriver::FFMPEG->value, new NullLogger());
        $media                = MediaFactory::random(MimeType::IMAGE, 'gif');

        $this->assertAndResizeFile($fileGifResizeService, $media->file(), Breakpoint::XSMALL);
    }

}
