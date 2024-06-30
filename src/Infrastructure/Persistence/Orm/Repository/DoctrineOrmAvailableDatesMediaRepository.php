<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ranky\MediaBundle\Domain\Contract\AvailableDatesMediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Model\MediaInterface;

/**
 * @extends ServiceEntityRepository<MediaInterface>
 */
class DoctrineOrmAvailableDatesMediaRepository extends ServiceEntityRepository implements
    AvailableDatesMediaRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     * @param class-string $mediaEntity
     */
    public function __construct(ManagerRegistry $registry, string $mediaEntity)
    {
        parent::__construct($registry, $mediaEntity);
    }


    /**
     * @return array|\Ranky\MediaBundle\Domain\Model\MediaInterface[]
     */
    public function getAll(): array
    {
        return $this
            ->createQueryBuilder('m')
            ->select('YEAR(m.createdAt) as year', 'MONTH(m.createdAt) as month', 'count(m) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
