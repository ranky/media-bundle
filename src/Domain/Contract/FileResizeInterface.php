<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;

interface FileResizeInterface
{
    public function resize(string $inputPath, string $outputPath, Dimension $dimension): void;

    public function support(File $file): bool;
}
