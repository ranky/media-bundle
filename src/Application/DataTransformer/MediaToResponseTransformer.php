<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Application\DataTransformer;


use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Domain\Contract\FileUrlResolver;
use Ranky\MediaBundle\Domain\Contract\UserMediaRepository;
use Ranky\MediaBundle\Domain\Model\Media;

class MediaToResponseTransformer
{

    public function __construct(
        private readonly UserMediaRepository $userMediaRepository,
        private readonly FileUrlResolver $fileUrlResolver,
        private readonly string $dateTimeFormat = MediaResponse::DATETIME_FORMAT,
    ) {
    }

    public function mediaToResponse(Media $media): MediaResponse
    {
        $createdBy = $this->userMediaRepository->getUsernameByUserIdentifier($media->createdBy());
        $updateBy  = $createdBy;
        if (!$media->createdBy()->equals($media->updatedBy())) {
            $updateBy = $this->userMediaRepository->getUsernameByUserIdentifier($media->updatedBy());
        }

        $mediaResponse = MediaResponse::fromMedia(
            $media,
            fn (string $path) => $this->fileUrlResolver->resolve($path),
            $createdBy,
            $updateBy
        );
        $mediaResponse->withDateTimeFormat($this->dateTimeFormat);

        return $mediaResponse;
    }

    /**
     * @param array<Media> $medias
     * @return array<MediaResponse>
     */
    public function mediaCollectionToArrayResponse(array $medias): array
    {
        $mediaResponse = [];
        foreach ($medias as $media) {
            $mediaResponse[] = $this->mediaToResponse($media);
        }

        return $mediaResponse;
    }

}
