<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Filesystem\Local;

use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;

final class LocalFilePathResolver implements FilePathResolverInterface
{

    public function __construct(private readonly string $uploadDirectory)
    {
    }

    public function resolve(?string $path = null): string
    {
        if (!$path) {
            return $this->uploadDirectory;
        }

        return \sprintf('%s/%s', $this->uploadDirectory, \trim($path, '/'));
    }

    public function resolveFromBreakpoint(string $breakpoint, string $path = null): string
    {

        if (!Breakpoint::tryFrom($breakpoint)){
            throw new \InvalidArgumentException(\sprintf('%s is not a valid value for Breakpoint enum', $breakpoint));
        }

        if (!$path) {
            return \sprintf('%s/%s', $this->uploadDirectory, $breakpoint);
        }

        return \sprintf('%s/%s/%s', $this->uploadDirectory, $breakpoint, \trim($path, '/'));

    }
}
