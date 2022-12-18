<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FileManipulation\Thumbnails\RenameThumbnails;

use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\FileUrlResolverInterface;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnail;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnails;

class RenameThumbnails
{

    public function __construct(
        private readonly FileRepositoryInterface $fileRepository,
        private readonly FileUrlResolverInterface $fileUrlResolver,
        private readonly FilePathResolverInterface $filePathResolver,
    ) {
    }

    public function __invoke(Thumbnails $thumbnails, string $newFileName): Thumbnails
    {
        $newThumbnails = new Thumbnails();
        /* @var Thumbnail $thumbnail */
        foreach ($thumbnails as $thumbnail) {
            $oldPath = $this->filePathResolver->resolveFromBreakpoint($thumbnail->breakpoint(), $thumbnail->name());
            $newPath = $this->filePathResolver->resolveFromBreakpoint($thumbnail->breakpoint(), $newFileName);
            $this->fileRepository->rename($oldPath, $newPath);

            $thumbnailPath = $this->fileUrlResolver->resolvePathFromBreakpoint($thumbnail->breakpoint(), $newFileName);
            $newThumbnails->add($thumbnail->rename($newFileName, $thumbnailPath));
        }

        return $newThumbnails;
    }

}
