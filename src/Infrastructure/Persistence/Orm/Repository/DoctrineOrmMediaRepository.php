<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Criteria\MediaCriteria;
use Ranky\MediaBundle\Domain\Exception\NotFoundMediaException;
use Ranky\MediaBundle\Domain\Model\MediaInterface;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\CriteriaBuilder\DoctrineCriteriaBuilderFactory;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;
use Ranky\SharedBundle\Infrastructure\Persistence\Orm\UidMapperPlatform;

/**
 * @extends ServiceEntityRepository<MediaInterface>
 * @method MediaInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaInterface[]    findAll()
 * @method MediaInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrineOrmMediaRepository extends ServiceEntityRepository implements MediaRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     * @param \Ranky\SharedBundle\Filter\CriteriaBuilder\DoctrineCriteriaBuilderFactory $doctrineCriteriaBuilderFactory
     * @param \Ranky\SharedBundle\Infrastructure\Persistence\Orm\UidMapperPlatform $uidMapperPlatform
     * @param class-string $mediaEntity
     */
    public function __construct(
        ManagerRegistry $registry,
        private readonly DoctrineCriteriaBuilderFactory $doctrineCriteriaBuilderFactory,
        private readonly UidMapperPlatform $uidMapperPlatform,
        string $mediaEntity
    ) {
        parent::__construct($registry, $mediaEntity);
    }

    public function nextIdentity(): MediaId
    {
        return MediaId::create();
    }


    public function getById(MediaId $id): MediaInterface
    {
        $media = $this->findOneBy([
            'id' => $id,
        ]);
        if (null === $media) {
            throw NotFoundMediaException::withId((string)$id);
        }

        return $media;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @return MediaInterface[]
     */
    public function findByIds(MediaId ...$ids): array
    {
        $mediaIds        = \array_map(fn(MediaId $mediaId) => $this->uidMapperPlatform->convertToDatabaseValue(
            $mediaId
        ), $ids);
        $criteria        = MediaCriteria::default();
        $orderPagination = $criteria->orderBy();

        return $this
            ->createQueryBuilder('m')
            ->where('m.id IN (:ids)')
            ->setParameter('ids', $mediaIds)
            ->orderBy($orderPagination->field(), $orderPagination->direction())
            ->getQuery()
            ->getResult();
    }

    public function filter(Criteria $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder($criteria::modelAlias());

        return $this->doctrineCriteriaBuilderFactory
            ->create($queryBuilder, $criteria)
            ->where()
            ->withLimit()
            ->withOrder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function size(?Criteria $criteria = null): int
    {
        if (!$criteria) {
            return $this->count([]);
        }

        $queryBuilder = $this
            ->createQueryBuilder($criteria::modelAlias())
            ->select('COUNT('.$criteria::modelAlias().'.id)');

        /**
         * @var int $result
         */
        $result = $this->doctrineCriteriaBuilderFactory
            ->create($queryBuilder, $criteria)
            ->where()
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function getByFilePath(string $filePath): MediaInterface
    {
        $media = $this->findOneBy(['file.path' => $filePath]);
        if (!$media) {
            throw NotFoundMediaException::withFilePath($filePath);
        }

        return $media;
    }

    /**
     * @return MediaInterface[]
     */
    public function findByFilePaths(array $filePaths): array
    {
        return $this->findBy(['file.path' => $filePaths]);
    }


    public function getByFileName(string $fileName): MediaInterface
    {
        $media = $this->findByFileName($fileName);
        if (!$media) {
            throw NotFoundMediaException::withFileName($fileName);
        }

        return $media;
    }

    public function findByFileName(string $value): ?MediaInterface
    {
        return $this->findOneBy(['file.name' => $value]);
    }

    /**
     * @return MediaInterface[]
     */
    public function findByFileNames(array $fileNames): array
    {
        return $this->findBy(['file.name' => $fileNames]);
    }

    public function getAll(OrderBy $orderPagination): array
    {
        return $this->queryBuilder($orderPagination)->getQuery()->getResult();
    }

    public function paginate(OffsetPagination $offsetPagination, OrderBy $orderPagination): array
    {
        return $this
            ->queryBuilder($orderPagination)
            ->setFirstResult(($offsetPagination->page() - 1) * $offsetPagination->limit())
            ->setMaxResults($offsetPagination->limit())
            ->getQuery()
            ->getResult();
    }

    private function queryBuilder(OrderBy $orderPagination): QueryBuilder
    {
        return $this
            ->createQueryBuilder('m')
            ->orderBy('m.'.$orderPagination->field(), $orderPagination->direction());
    }

    public function save(MediaInterface $media): void
    {
        $this->getEntityManager()->persist($media);
        $this->getEntityManager()->flush();
    }

    public function delete(MediaInterface $media): void
    {
        $this->getEntityManager()->remove($media);
        $this->getEntityManager()->flush();
    }
}
