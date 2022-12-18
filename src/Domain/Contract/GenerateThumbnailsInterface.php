<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

use Ranky\MediaBundle\Domain\ValueObject\File;

interface GenerateThumbnailsInterface
{
    public function generate(string $mediaId): void;

    public function support(File $file): bool;
}
