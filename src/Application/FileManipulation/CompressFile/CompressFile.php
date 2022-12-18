<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FileManipulation\CompressFile;

use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Service\FileCompressHandler;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnails;

/**
 * Compressing files once files have been resized
 */
class CompressFile
{

    public function __construct(
        private readonly bool $disableCompression,
        private readonly bool $compressOnlyOriginal,
        private readonly FileCompressHandler $compressHandler,
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly FileRepositoryInterface $fileRepository,
        private readonly FilePathResolverInterface $filePathResolver,
    ) {
    }

    public function __invoke(string $mediaId): void
    {
        if ($this->disableCompression) {
            return;
        }
        $media = $this->mediaRepository->getById(MediaId::fromString($mediaId));
        $path  = $this->filePathResolver->resolve($media->file()->path());
        // compress main image
        if(!$this->compressHandler->compress($path, $media->file())) {
            return;
        }
        $oldSize = $media->file()->size();
        $newSize = $this->fileRepository->filesizeFromPath($path);
        if ($newSize !== $oldSize) {
            $media->updateFileSize($newSize);
            $this->mediaRepository->save($media);
        }
        if ($this->compressOnlyOriginal) {
            return;
        }

        // compress thumbnails
        $thumbnails       = $media->thumbnails();
        $updateThumbnails = new Thumbnails();
        $needUpdate       = false;

        /* @var \Ranky\MediaBundle\Domain\ValueObject\Thumbnail $thumbnail */
        foreach ($thumbnails as $thumbnail) {
            $thumbnailPath = $this->filePathResolver->resolveFromBreakpoint(
                $thumbnail->breakpoint(),
                $media->file()->path()
            );
            $oldSize       = $thumbnail->size();
            if (!$this->compressHandler->compress($thumbnailPath, $media->file())) {
                continue;
            }
            $newSize = $this->fileRepository->filesizeFromPath($thumbnailPath);
            if ($newSize !== $oldSize) {
                $needUpdate = true;
                $updateThumbnails->add($thumbnail->updateSize($newSize));
            } else {
                $updateThumbnails->add($thumbnail);
            }
        }
        if ($needUpdate) {
            $media->updateThumbnails($updateThumbnails);
            $this->mediaRepository->save($media);
        }
    }
}
