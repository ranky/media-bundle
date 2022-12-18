<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Exception\NotFoundMediaException;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\CriteriaBuilder\DoctrineCriteriaBuilderFactory;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;

/**
 * @extends ServiceEntityRepository<Media>
 */
final class DoctrineOrmMediaRepository extends ServiceEntityRepository implements MediaRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly DoctrineCriteriaBuilderFactory $doctrineCriteriaBuilderFactory
    ) {
        parent::__construct($registry, Media::class);
    }

    public function nextIdentity(): MediaId
    {
        return MediaId::generate();
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

        return $this->doctrineCriteriaBuilderFactory
            ->create($queryBuilder, $criteria)
            ->where()
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getByFileName(string $fileName): Media
    {
        $media = $this->findOneBy(['file.name' => $fileName]);
        if (!$media) {
            throw NotFoundMediaException::withFileName($fileName);
        }

        return $media;
    }

    /**
     * @return Media[]
     */
    public function findByFileNames(array $fileNames): array
    {
        return $this->findBy(['file.name' => $fileNames]);
    }

    public function getById(MediaId $id): Media
    {
        $media = $this->find($id);
        if (null === $media) {
            throw NotFoundMediaException::withId((string)$id);
        }

        return $media;
    }

    /**
     * @return Media[]
     */
    public function findByIds(MediaId ...$ids): array
    {
        return $this->findBy([
            'id' => \array_map(static fn (MediaId $mediaId) => $mediaId->asBinary(), $ids),
        ]);
    }

    public function getAll(OrderBy $orderPagination): array
    {
        return $this->getAllQueryBuilder($orderPagination)->getQuery()->getResult();
    }

    public function paginate(OffsetPagination $offsetPagination, OrderBy $orderPagination): array
    {
        return $this
            ->getAllQueryBuilder($orderPagination)
            ->setFirstResult(($offsetPagination->page() - 1) * $offsetPagination->limit())
            ->setMaxResults($offsetPagination->limit())
            ->getQuery()
            ->getResult();
    }

    private function getAllQueryBuilder(OrderBy $orderPagination): QueryBuilder
    {
        return $this
            ->createQueryBuilder('m')
            ->orderBy('m.'.$orderPagination->field(), $orderPagination->direction());
    }

    public function save(Media $media): void
    {
        $this->getEntityManager()->persist($media);
        $this->getEntityManager()->flush();
    }

    public function delete(Media $media): void
    {
        $this->getEntityManager()->remove($media);
        $this->getEntityManager()->flush();
    }
}
