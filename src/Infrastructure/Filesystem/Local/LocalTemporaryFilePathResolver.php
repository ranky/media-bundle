<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Filesystem\Local;

use Ranky\MediaBundle\Domain\Contract\FilePathResolver;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Exception\InvalidBreakpointException;
use Ranky\MediaBundle\Domain\Service\ThumbnailPathResolver;
use Ranky\SharedBundle\Common\FileHelper;

final class LocalTemporaryFilePathResolver implements FilePathResolver
{
    public function __construct(
        private readonly string $temporaryDirectory
    ) {
    }

    public function resolve(?string $path = null, ?string $breakpoint = null): string
    {
        if ($path && (
            \str_starts_with($path, $this->temporaryDirectory) || \str_starts_with($path, \sys_get_temp_dir())
        )) {
            return $path;
        }
        if ($breakpoint && !Breakpoint::tryFrom($breakpoint)) {
            throw InvalidBreakpointException::withName($breakpoint);
        }

        if ($breakpoint && $path) {
            $path = ThumbnailPathResolver::resolve(
                $path,
                $breakpoint,
                \DIRECTORY_SEPARATOR
            );
        }

        return FileHelper::pathJoin($this->temporaryDirectory, $path ?? '');
    }
}
