<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;

interface GenerateThumbnails
{
    public function generate(string $mediaId, File $file, Dimension $dimension): void;

    public function support(File $file): bool;
}
