<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\GetMedia;


use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;

class GetMediaById
{

    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly MediaToResponseTransformer $responseTransformer
    ) {
    }


    public function __invoke(string $mediaId): MediaResponse
    {
        $media = $this->mediaRepository->getById(MediaId::fromString($mediaId));

        return $this->responseTransformer->mediaToResponse($media);
    }


}
