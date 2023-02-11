<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\FileManipulation\Compression;

use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Compression\SpatieFileCompression;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class SpatieFileCompressionTest extends BaseIntegrationTestCase
{
    private SpatieFileCompression $fileCompress;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileCompress = $this->getService(SpatieFileCompression::class);
    }

    public function testItShouldCompressJpgWithJpegoptim(): void
    {
        $jpegoptimCanBeUsed = (bool)@shell_exec('jpegoptim --version');
        if (!$jpegoptimCanBeUsed) {
            $this->markTestSkipped('The jpegoptim program is not available.');
        }

        $media = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $this->compressAndAssertFile($media->file());
    }

    public function testItShouldCompressGifWithGifsicle(): void
    {
        $gifsicleCanBeUsed = (bool)@shell_exec('gifsicle --version');
        if (!$gifsicleCanBeUsed) {
            $this->markTestSkipped('The gifsicle program is not available.');
        }

        $media = MediaFactory::random(MimeType::IMAGE, 'gif');

        $this->compressAndAssertFile($media->file());
    }

    public function testItShouldCompressPngWithPngquant(): void
    {
        $pngquantCanBeUsed = (bool)@shell_exec('pngquant --version');
        if (!$pngquantCanBeUsed) {
            $this->markTestSkipped('The pngquant program is not available.');
        }

        $media = MediaFactory::random(MimeType::IMAGE, 'png');

        $this->compressAndAssertFile($media->file());
    }

    public function testItShouldCompressPngWithOptipng(): void
    {
        $optipngCanBeUsed = (bool)@shell_exec('optipng --version');
        if (!$optipngCanBeUsed) {
            $this->markTestSkipped('The optipng program is not available.');
        }

        $media = MediaFactory::random(MimeType::IMAGE, 'png');

        $this->compressAndAssertFile($media->file());
    }

    public function testItShouldCompressWebpWithCwebp(): void
    {
        $webpCanBeUsed = (bool)@shell_exec('cwebp -version');
        if (!$webpCanBeUsed) {
            $this->markTestSkipped('The cwebp program is not available.');
        }

        $media = MediaFactory::random(MimeType::IMAGE, 'webp');

        $this->compressAndAssertFile($media->file());
    }

    private function compressAndAssertFile(File $file): void
    {
        $this->assertTrue($this->fileCompress->support($file));

        $currentFilePath = $this->getDummyDirectory().'/'.$file->name();
        $newFilePath     = $this->getTempFileDirectory().'/'.$file->baseName().'_compress.'.$file->extension();

        copy($currentFilePath, $newFilePath);
        $this->assertFileExists($newFilePath);
        $this->fileCompress->compress($newFilePath);
        $this->assertGreaterThan(\filesize($newFilePath), $file->size(), "File: $newFilePath");
        unlink($newFilePath);
    }

}
