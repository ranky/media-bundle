<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\ListMediaCriteria;


use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Pagination\Pagination;

class ListMediaCriteria
{

    public function __construct(
        private readonly MediaRepository $mediaRepository,
        private readonly MediaToResponseTransformer $responseTransformer
    ) {
    }


    /**
     * @param Criteria $criteria
     * @return Pagination<\Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse>
     */
    public function __invoke(Criteria $criteria): Pagination
    {
        $results = $this->mediaRepository->filter($criteria);
        $count   = $this->mediaRepository->size($criteria);


        return new Pagination(
            $this->responseTransformer->mediaCollectionToArrayResponse($results),
            $count,
            $criteria->offsetPagination()
        );
    }


}
