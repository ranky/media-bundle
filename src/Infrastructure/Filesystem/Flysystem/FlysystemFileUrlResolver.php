<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem;

use Ranky\MediaBundle\Domain\Contract\FileUrlResolver;
use Ranky\MediaBundle\Domain\Service\ThumbnailPathResolver;
use Ranky\SharedBundle\Domain\Site\SiteUrlResolverInterface;

final class FlysystemFileUrlResolver implements FileUrlResolver
{
    public function __construct(
        private readonly string $uploadUrl,
        private readonly string $rankyMediaStorageAdapter,
        private readonly SiteUrlResolverInterface $siteUrlResolver,
    ) {
    }

    public function resolve(string $path, ?string $breakpoint = null, bool $absolute = true): string
    {
        $uploadUrl = $this->uploadUrl;
        $siteUrl   = null;

        // if is full URL, then fix path and define siteUrl
        if (\str_contains($path, '://')) {
            $parsePath  = \parse_url($path);
            $path = $parsePath['path'] ?? '/';
            $port = '';
            if (isset($parsePath['port']) && $parsePath['port'] !== 80 && $parsePath['port'] !== 443) {
                $port = ':'.$parsePath['port'];
            }
            if (isset($parsePath['scheme'], $parsePath['host'])) {
                $siteUrl  = \sprintf('%s://%s%s', $parsePath['scheme'], $parsePath['host'], $port);
            }
            if (\str_contains($path, $uploadUrl)) {
                $path = \str_replace($uploadUrl, '', $path);
            }
        }
        $path = '/'.\ltrim($path, '/');
        if (\str_contains($uploadUrl, '://')) {
            $parseUrl  = \parse_url($uploadUrl);
            $uploadUrl = $parseUrl['path'] ?? '/';
            $port = '';
            if (isset($parseUrl['port']) && $parseUrl['port'] !== 80 && $parseUrl['port'] !== 443) {
                $port = ':'.$parseUrl['port'];
            }
            if (isset($parseUrl['scheme'], $parseUrl['host'])) {
                $siteUrl  = \sprintf('%s://%s%s', $parseUrl['scheme'], $parseUrl['host'], $port);
            }
        }
        if ($path !== '/' && $uploadUrl !== '/' && \str_contains($path, $uploadUrl)) {
            $path = \str_replace($uploadUrl, '', $path);
        }

        $publicPath = '/'.$this->pathJoin(
            $uploadUrl,
            $breakpoint ? ThumbnailPathResolver::resolve($path, $breakpoint) : $path
        );

        if (!$absolute) {
            return \rtrim($publicPath, '/');
        }

        if ($this->rankyMediaStorageAdapter === 'local' && !$siteUrl) {
            return \rtrim($this->siteUrlResolver->siteUrl($publicPath), '/');
        }

        return \sprintf('%s/%s', $siteUrl ?? '', \trim($publicPath, '/'));
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
