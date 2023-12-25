<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Presentation\Twig;

use Doctrine\Common\Collections\Collection;
use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\FindMedia\FindMediaByIds;
use Ranky\MediaBundle\Application\FindMedia\FindMediaByPaths;
use Ranky\MediaBundle\Application\GetMedia\GetMediaByFilePath;
use Ranky\MediaBundle\Application\GetMedia\GetMediaById;
use Ranky\MediaBundle\Domain\Exception\NotFoundMediaException;
use Ranky\MediaBundle\Domain\Model\Media;

class MediaTwigService
{


    public function __construct(
        private readonly GetMediaByFilePath $getMediaByFilePath,
        private readonly FindMediaByPaths $findMediaByPaths,
        private readonly GetMediaById $getMediaById,
        private readonly FindMediaByIds $findMediaByIds,
        private readonly MediaToResponseTransformer $responseTransformer,
    ) {
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

    public function mediaToResponse(Media $media): ?MediaResponse
    {
        return $this->responseTransformer->mediaToResponse($media);
    }

    /**
     * @param Collection<int,Media> $mediaCollection
     * @return array<MediaResponse>
     */
    public function mediaCollectionToArrayResponse(Collection $mediaCollection): array
    {
        return $this->responseTransformer->mediaCollectionToArrayResponse($mediaCollection->toArray());
    }
}
