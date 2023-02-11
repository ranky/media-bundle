<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FindMedia;


use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;

class FindMediaByIds
{

    public function __construct(
        private readonly MediaRepository $mediaRepository,
        private readonly MediaToResponseTransformer $responseTransformer,
    ) {
    }

    /**
     * @param array<string> $ids
     * @return array<MediaResponse>
     */
    public function __invoke(array $ids): array
    {
        $mediaIds = array_map(static fn(string $id) => MediaId::fromString($id), $ids);
        $results        = $this->mediaRepository->findByIds(
            ...$mediaIds
        );

        return $this->responseTransformer->mediaCollectionToArrayResponse($results);
    }


}
