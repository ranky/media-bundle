<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Twig;

use Ranky\MediaBundle\Domain\Contract\FileUrlResolverInterface;
use Ranky\MediaBundle\Domain\Model\MediaInterface;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
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
            new TwigFilter('isMediaId', [$this, 'isMediaId']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ranky_media_url', [$this, 'mediaUrl']),
            new TwigFunction('ranky_media_thumbnail_url', [$this, 'mediaThumbnailUrl']),
        ];
    }

    public function isMediaId(mixed $value): bool
    {
        if (\is_array($value) && !empty($value)) {
            $value = \reset($value);
        }

        return $value && (($value instanceof MediaId || MediaId::isValid($value)));
    }

    /**
     * @param string|MediaInterface $path The the file path or media entity
     * @param bool $absolute
     * @return string
     */
    public function mediaUrl(string|MediaInterface $path, bool $absolute = false): string
    {
        if ($path instanceof MediaInterface) {
            $path = $path->file()->path();
        }

        return $this->fileUrlResolver->resolve($path, $absolute);
    }

    /**
     * @param string|MediaInterface $path The file path or media entity
     * @param string $breakpoint
     * @param bool $absolute
     * @return string
     */
    public function mediaThumbnailUrl(string|MediaInterface $path, string $breakpoint, bool $absolute = false): string
    {
        if ($path instanceof MediaInterface) {
            $path = $path->file()->path();
        }
        return $this->fileUrlResolver->resolveFromBreakpoint($breakpoint, $path, $absolute);
    }
}
