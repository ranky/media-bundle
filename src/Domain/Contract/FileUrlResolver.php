<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

interface FileUrlResolver
{
    public function resolve(string $path, ?string $breakpoint = null, bool $absolute = true): string;

}
