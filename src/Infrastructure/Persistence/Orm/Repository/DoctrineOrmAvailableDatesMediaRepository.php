<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ranky\MediaBundle\Domain\Contract\AvailableDatesMediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Model\Media;

/**
 * @extends ServiceEntityRepository<Media>
 */
final class DoctrineOrmAvailableDatesMediaRepository extends ServiceEntityRepository implements
    AvailableDatesMediaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }


    /**
     * @return array|\Ranky\MediaBundle\Domain\Model\Media[]
     */
    public function getAll(): array
    {
        return $this
            ->createQueryBuilder('m')
            ->select('YEAR(m.createdAt) as year', 'MONTH(m.createdAt) as month', 'count(m) as count')
            ->groupBy('year', 'month')
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
