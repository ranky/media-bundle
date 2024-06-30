<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ranky\MediaBundle\Domain\Contract\MimeMediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Model\MediaInterface;

/**
 * @extends ServiceEntityRepository<MediaInterface>
 */
class DoctrineOrmMimeMediaRepository extends ServiceEntityRepository implements MimeMediaRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     * @param class-string $mediaEntity
     */
    public function __construct(ManagerRegistry $registry, string $mediaEntity)
    {
        parent::__construct($registry, $mediaEntity);
    }


    public function getAll(): array
    {
        return $this
            ->createQueryBuilder('m')
            ->select('m.file.mime as mime', 'count(m) as count')
            ->groupBy('m.file.mime')
            ->getQuery()
            ->getResult();
    }

    public function getAllByType(): array
    {
        return $this
            ->createQueryBuilder('m')
            ->select('MIME_TYPE(m.file.mime) as mimeType', 'count(m) as count')
            ->groupBy('mimeType')
            ->getQuery()->getResult();
    }

    public function getAllBySubType(): array
    {
        return $this
            ->createQueryBuilder('m')
            ->select('MIME_SUBTYPE(m.file.mime) as mimeSubType', 'count(m) as count')
            ->groupBy('mimeSubType')
            ->getQuery()
            ->getResult();
    }

}
