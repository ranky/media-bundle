<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;


use Ranky\MediaBundle\Domain\Model\MediaInterface;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;

interface MediaRepositoryInterface
{


    public function nextIdentity(): MediaId;

    /**
     * @return MediaInterface[]
     */
    public function getAll(OrderBy $orderPagination): array;

    public function size(?Criteria $criteria = null): int;

    /**
     * @param \Ranky\SharedBundle\Filter\Criteria $criteria
     * @return MediaInterface[]
     */
    public function filter(Criteria $criteria): array;

    /**
     * @param \Ranky\SharedBundle\Filter\Pagination\OffsetPagination $offsetPagination
     * @param \Ranky\SharedBundle\Filter\Order\OrderBy $orderPagination
     * @return MediaInterface[]
     */
    public function paginate(OffsetPagination $offsetPagination, OrderBy $orderPagination): array;

    public function getById(MediaId $id): MediaInterface;

    /**
     * @param MediaId ...$ids
     * @return MediaInterface[]
     */
    public function findByIds(MediaId ...$ids): array;

    public function getByFilePath(string $filePath): MediaInterface;

    /**
     * @param string[] $filePaths
     * @return MediaInterface[]
     */
    public function findByFilePaths(array $filePaths): array;

    public function getByFileName(string $fileName): MediaInterface;

    public function findByFileName(string $value): ?MediaInterface;

    /**
     * @param string[] $fileNames
     * @return MediaInterface[]
     */
    public function findByFileNames(array $fileNames): array;


    public function save(MediaInterface $media): void;

    public function delete(MediaInterface $media): void;

}
