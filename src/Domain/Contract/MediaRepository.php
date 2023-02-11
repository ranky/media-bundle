<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;


use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;

interface MediaRepository
{


    public function nextIdentity(): MediaId;

    /**
     * @return Media[]
     */
    public function getAll(OrderBy $orderPagination): array;

    public function size(?Criteria $criteria = null): int;

    /**
     * @param \Ranky\SharedBundle\Filter\Criteria $criteria
     * @return Media[]
     */
    public function filter(Criteria $criteria): array;

    /**
     * @param \Ranky\SharedBundle\Filter\Pagination\OffsetPagination $offsetPagination
     * @param \Ranky\SharedBundle\Filter\Order\OrderBy $orderPagination
     * @return Media[]
     */
    public function paginate(OffsetPagination $offsetPagination, OrderBy $orderPagination): array;

    public function getById(MediaId $id): Media;

    public function getByFileName(string $fileName): Media;

    /**
     * @param MediaId ...$ids
     * @return Media[]
     */
    public function findByIds(MediaId ...$ids): array;

    /**
     * @param string[] $fileNames
     * @return Media[]
     */
    public function findByFileNames(array $fileNames): array;

    public function save(Media $media): void;

    public function delete(Media $media): void;
}
