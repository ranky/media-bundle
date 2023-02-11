<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Domain\Service;

use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Exception\InvalidBreakpointException;

class ThumbnailPathResolver
{
    public static function resolve(string $path, string $breakpoint, string $directorySeparator = '/'): string
    {
        $path = self::prepare($path, $breakpoint);

        return \sprintf(
            '%s%s%s%s',
            $directorySeparator,
            $breakpoint,
            $directorySeparator,
            \ltrim($path, $directorySeparator)
        );
    }

    public static function prepare(string $path, string $breakpoint, string $directorySeparator = '/'): string
    {
        if (!Breakpoint::tryFrom($breakpoint)) {
            throw InvalidBreakpointException::withName($breakpoint);
        }

        if (\str_contains($path, $directorySeparator.$breakpoint)) {
            return \str_replace($directorySeparator.$breakpoint, '', $path);
        }

        return $path;
    }
}
