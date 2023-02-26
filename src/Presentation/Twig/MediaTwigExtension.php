<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Twig;

use Ranky\MediaBundle\Domain\Contract\FileUrlResolver;
use Ranky\SharedBundle\Common\FileHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class MediaTwigExtension extends AbstractExtension
{

    public function __construct(
        private readonly FileUrlResolver $fileUrlResolver
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
        ];
    }

    public function mediaUrl(string $fileName, ?string $breakpoint = null): string
    {
        return $this->fileUrlResolver->resolve($fileName, $breakpoint);
    }
}
