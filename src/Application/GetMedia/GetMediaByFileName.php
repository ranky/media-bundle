<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\GetMedia;


use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;

class GetMediaByFileName
{

    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly MediaToResponseTransformer $responseTransformer
    ) {
    }


    public function __invoke(string $fileName): MediaResponse
    {
        $media = $this->mediaRepository->getByFileName($fileName);

        return $this->responseTransformer->mediaToResponse($media);
    }


}
