<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FileManipulation\Thumbnails\GenerateThumbnails;

use Ranky\MediaBundle\Domain\Contract\GenerateThumbnailsInterface;
use Ranky\MediaBundle\Domain\ValueObject\File;

final class GenerateGifImageThumbnails extends AbstractGenerateImageThumbnails implements GenerateThumbnailsInterface
{

    public function support(File $file): bool
    {
        return $file->extension() === 'gif';
    }
}
