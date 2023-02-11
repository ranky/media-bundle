<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

interface FilePathResolver
{
    public function resolve(?string $path = null, ?string $breakpoint = null): string;
}
