<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Twig;

use Ranky\MediaBundle\Domain\Contract\FileUrlResolverInterface;
use Ranky\SharedBundle\Common\FileHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class MediaTwigExtension extends AbstractExtension
{

    public function __construct(
        private readonly FileUrlResolverInterface $fileUrlResolver
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('human_file_size', [FileHelper::class, 'humanFileSize']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ranky_media_url', [$this, 'mediaUrl']),
            new TwigFunction('ranky_media_thumbnail_url', [$this, 'mediaThumbnailUrl']),
        ];
    }

    public function mediaUrl(string $fileName, bool $absolute = false): string
    {
        return $this->fileUrlResolver->resolve($fileName, $absolute);
    }

    public function mediaThumbnailUrl(string $fileName, string $breakpoint, bool $absolute = false): string
    {
        return $this->fileUrlResolver->resolveFromBreakpoint($breakpoint, $fileName, $absolute);
    }
}
