<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem;

use Ranky\MediaBundle\Domain\Contract\FileUrlResolver;
use Ranky\MediaBundle\Domain\Service\ThumbnailPathResolver;

final class FlysystemFileUrlResolver implements FileUrlResolver
{
    public function __construct(private readonly string $uploadUrl)
    {
    }

    public function resolve(string $path, ?string $breakpoint = null): string
    {
        $uploadUrl = $this->uploadUrl;

        if ($path !== '/' && $uploadUrl !== '/' && \str_contains($path, $uploadUrl)) {
            $path = \str_replace($uploadUrl, '', $path);
        }

        $publicPath = $this->pathJoin(
            $uploadUrl,
            $breakpoint ? ThumbnailPathResolver::resolve($path, $breakpoint) : $path
        );

        if (!\str_contains($publicPath, '://')) {
            return '/'.ltrim($publicPath, '/');
        }

        return $publicPath;
    }

    private function pathJoin(string ...$paths): string
    {
        $cleanPaths = \array_map(static fn ($path) => \trim($path, '/'), $paths);

        return \sprintf(
            '%s',
            \implode('/', \array_filter($cleanPaths))
        );
    }
}
