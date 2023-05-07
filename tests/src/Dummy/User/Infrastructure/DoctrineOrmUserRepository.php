<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Dummy\User\Infrastructure;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ranky\MediaBundle\Tests\Dummy\User\Domain\UserRepository;
use Ranky\MediaBundle\Tests\Dummy\User\Domain\User;

/**
 * @extends ServiceEntityRepository<User>
 */
final class DoctrineOrmUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    public function getAll(): array
    {
        return $this->findAll();
    }

    public function getById(int $id): ?User
    {
        return $this->find($id);
    }

    public function getByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function delete(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}
