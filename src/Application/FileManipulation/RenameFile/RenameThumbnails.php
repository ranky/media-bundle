<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FileManipulation\RenameFile;

use Ranky\MediaBundle\Domain\Contract\FileRepository;
use Ranky\MediaBundle\Domain\Service\ThumbnailPathResolver;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnail;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnails;

class RenameThumbnails
{

    public function __construct(
        private readonly FileRepository $fileRepository,
    ) {
    }

    public function __invoke(Thumbnails $thumbnails, string $newFileName): Thumbnails
    {
        $newThumbnails = new Thumbnails();
        /* @var Thumbnail $thumbnail */
        foreach ($thumbnails as $thumbnail) {
            $oldPath = $thumbnail->path();
            $newPath = ThumbnailPathResolver::resolve($newFileName, $thumbnail->breakpoint());
            $this->fileRepository->rename($oldPath, $newPath);
            $newThumbnails->add($thumbnail->rename($newFileName, $newPath));
        }

        return $newThumbnails;
    }

}
