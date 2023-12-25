<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FindMedia;


use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;

class FindMediaByPaths
{

    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly MediaToResponseTransformer $responseTransformer,
    ) {
    }

    /**
     * @param array<string> $paths
     * @return array<MediaResponse>
     */
    public function __invoke(array $paths): array
    {
        $results = $this->mediaRepository->findByFilePaths($paths);

        return $this->responseTransformer->mediaCollectionToArrayResponse($results);
    }


}
