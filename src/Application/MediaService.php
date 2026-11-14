<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Application;

use Doctrine\Common\Collections\Collection;
use Ranky\MediaBundle\Application\CreateMedia\CreateMedia;
use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\FindMedia\FindMediaByIds;
use Ranky\MediaBundle\Application\FindMedia\FindMediaByPaths;
use Ranky\MediaBundle\Application\GetMedia\GetMediaByFilePath;
use Ranky\MediaBundle\Application\GetMedia\GetMediaById;
use Ranky\MediaBundle\Domain\Exception\NotFoundMediaException;
use Ranky\MediaBundle\Domain\Model\MediaInterface;

class MediaService
{


    public function __construct(
        private readonly CreateMedia $createMedia,
        private readonly GetMediaByFilePath $getMediaByFilePath,
        private readonly FindMediaByPaths $findMediaByPaths,
        private readonly GetMediaById $getMediaById,
        private readonly FindMediaByIds $findMediaByIds,
        private readonly MediaToResponseTransformer $responseTransformer,
    ) {
    }

    public function uploadFileFromPath(string $filePath, ?string $userIdentifier = null): MediaResponse
    {
        if (!\is_file($filePath) || !\is_readable($filePath)) {
            throw new \InvalidArgumentException(\sprintf('File "%s" does not exist or is not readable.', $filePath));
        }

        $fileSize = \filesize($filePath);
        $mimeType = \mime_content_type($filePath);
        $extension = \pathinfo($filePath, \PATHINFO_EXTENSION);
        if (false === $fileSize || false === $mimeType || '' === $extension) {
            throw new \RuntimeException(\sprintf('Could not read the file metadata for "%s".', $filePath));
        }

        $temporaryFilePath = \tempnam(\sys_get_temp_dir(), 'ranky-media-upload-');
        if (false === $temporaryFilePath) {
            throw new \RuntimeException('Could not create a temporary file for the media upload.');
        }

        if (!\copy($filePath, $temporaryFilePath)) {
            \unlink($temporaryFilePath);
            throw new \RuntimeException(\sprintf('Could not copy "%s" for the media upload.', $filePath));
        }

        try {
            return $this->createMedia->__invoke(
                new UploadedFileRequest(
                    $temporaryFilePath,
                    \basename($filePath),
                    $mimeType,
                    \strtolower($extension),
                    $fileSize,
                ),
                $userIdentifier,
            );
        } finally {
            if (\file_exists($temporaryFilePath)) {
                \unlink($temporaryFilePath);
            }
        }
    }

    public function findByPath(string $path): ?MediaResponse
    {
        try {
            return $this->getMediaByFilePath->__invoke($path);
        } catch (NotFoundMediaException) {
            return null;
        }
    }

    /**
     * @param array<string> $paths
     * @return array<MediaResponse>
     */
    public function findByPaths(array $paths): array
    {
        return $this->findMediaByPaths->__invoke($paths);
    }

    public function findById(string $mediaId): ?MediaResponse
    {
        try {
            return $this->getMediaById->__invoke($mediaId);
        } catch (NotFoundMediaException) {
            return null;
        }
    }

    /**
     * @param array<string> $ids
     * @return array<MediaResponse>
     */
    public function findByIds(array $ids): array
    {
        return $this->findMediaByIds->__invoke($ids);
    }

    public function mediaToResponse(MediaInterface $media): ?MediaResponse
    {
        return $this->responseTransformer->mediaToResponse($media);
    }

    /**
     * @param Collection<int,MediaInterface> $mediaCollection
     * @return array<MediaResponse>
     */
    public function mediaCollectionToArrayResponse(Collection $mediaCollection): array
    {
        return $this->responseTransformer->mediaCollectionToArrayResponse($mediaCollection->toArray());
    }
}
