<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FileManipulation\GenerateThumbnails;

use Ranky\MediaBundle\Domain\Contract\GenerateThumbnails;
use Ranky\MediaBundle\Domain\ValueObject\File;

final class GenerateGifImageThumbnails extends AbstractGenerateImageThumbnails implements GenerateThumbnails
{

    public function support(File $file): bool
    {
        return $file->extension() === 'gif';
    }
}
