<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\GetMedia;


use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;

class GetMediaByFilePath
{

    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly MediaToResponseTransformer $responseTransformer
    ) {
    }


    public function __invoke(string $filePath): MediaResponse
    {
        $media = $this->mediaRepository->getByFilePath($filePath);

        return $this->responseTransformer->mediaToResponse($media);
    }


}
