<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FileManipulation\CompressFile;

use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Contract\TemporaryFileRepository;
use Ranky\MediaBundle\Domain\Service\FileCompressHandler;
use Ranky\MediaBundle\Domain\Service\ThumbnailPathResolver;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;

/**
 * Compressing files once files have been resized
 */
class CompressFile
{
    /**
     * @param bool $disableCompression
     * @param bool $compressOnlyOriginal
     * @param array<string, mixed> $breakpoints
     * @param FileCompressHandler $compressHandler
     * @param MediaRepository $mediaRepository
     * @param TemporaryFileRepository $temporaryFileRepository
     */
    public function __construct(
        private readonly bool $disableCompression,
        private readonly bool $compressOnlyOriginal,
        private readonly array $breakpoints,
        private readonly FileCompressHandler $compressHandler,
        private readonly MediaRepository $mediaRepository,
        private readonly TemporaryFileRepository $temporaryFileRepository,
    ) {
    }

    public function __invoke(string $mediaId): void
    {
        if ($this->disableCompression) {
            return;
        }
        $media = $this->mediaRepository->getById(MediaId::fromString($mediaId));
        $file  = $media->file();
        /* compress main image */
        $path = $this->temporaryFileRepository->temporaryFile($file->path());
        if (!$this->compressHandler->compress($file, $path)) {
            return;
        }
        if ($this->compressOnlyOriginal) {
            return;
        }
        /* compress thumbnails */
        foreach ($this->breakpoints as $nameBreakpoint => $dimensionBreakpoint) {
            $thumbnailPath          = ThumbnailPathResolver::resolve($file->path(), $nameBreakpoint);
            $temporaryThumbnailPath = $this->temporaryFileRepository->temporaryFile($thumbnailPath);
            if (!$this->temporaryFileRepository->exists($temporaryThumbnailPath)) {
                continue;
            }
            $this->compressHandler->compress($file, $temporaryThumbnailPath);
        }
    }
}
