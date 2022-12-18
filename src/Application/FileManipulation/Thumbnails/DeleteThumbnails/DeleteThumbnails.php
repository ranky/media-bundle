<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FileManipulation\Thumbnails\DeleteThumbnails;

use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;

final class DeleteThumbnails
{

    public function __construct(
        private readonly FileRepositoryInterface $fileRepository
    ) {
    }

    /**
     * @param array<int|string, array<string, mixed>> $thumbnails
     * @return void
     */
    public function delete(array $thumbnails): void
    {
        foreach ($thumbnails as $thumbnail) {
            $this->fileRepository->delete($thumbnail['path']);
        }
    }
}
