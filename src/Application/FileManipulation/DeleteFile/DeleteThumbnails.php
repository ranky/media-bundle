<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FileManipulation\DeleteFile;

use Ranky\MediaBundle\Domain\Contract\FileRepository;

final class DeleteThumbnails
{

    public function __construct(
        private readonly FileRepository $fileRepository
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
