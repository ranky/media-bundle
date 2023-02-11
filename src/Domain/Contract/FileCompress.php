<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

use Ranky\MediaBundle\Domain\ValueObject\File;

interface FileCompress
{
    public function compress(string $path): void;

    public function support(File $file): bool;
}
