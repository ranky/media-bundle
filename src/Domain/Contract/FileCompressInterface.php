<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

use Ranky\MediaBundle\Domain\ValueObject\File;

interface FileCompressInterface
{
    public function compress(string $absolutePath): void;

    public function support(File $file): bool;
}
