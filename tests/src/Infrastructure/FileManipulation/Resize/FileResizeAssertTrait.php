<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\FileManipulation\Resize;

use Ranky\MediaBundle\Domain\Contract\FileResizeInterface;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;

trait FileResizeAssertTrait
{

    public function assertAndResizeFile(FileResizeInterface $fileResize, File $file, Breakpoint $breakpoint): void
    {

        $this->assertTrue($fileResize->support($file));
        $inputPath  = $this->getDummyDir().'/'.$file->name();
        $outputPath = $this->getTempFileDir().'/'.$file->baseName().'_resize.'.$file->extension();
        $dimension  = Dimension::fromArray($breakpoint->dimensions());

        $fileResize->resize($inputPath, $outputPath, $dimension);
        $this->assertFileExists($outputPath);
        $actualDimension = @getimagesize($outputPath);
        $this->assertSame($dimension->width(), $actualDimension[0] ?? 0);
        unlink($outputPath);
    }

}
