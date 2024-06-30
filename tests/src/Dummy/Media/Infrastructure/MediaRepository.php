<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Dummy\Media\Infrastructure;

use Doctrine\Persistence\ManagerRegistry;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMediaRepository;
use Ranky\MediaBundle\Tests\Dummy\Media\Domain\Media;
use Ranky\SharedBundle\Filter\CriteriaBuilder\DoctrineCriteriaBuilderFactory;
use Ranky\SharedBundle\Infrastructure\Persistence\Orm\UidMapperPlatform;

class MediaRepository extends DoctrineOrmMediaRepository
{
    public function __construct(
        ManagerRegistry $registry,
        DoctrineCriteriaBuilderFactory $doctrineCriteriaBuilderFactory,
        UidMapperPlatform $uidMapperPlatform,
    ) {
        parent::__construct(
            $registry,
            $doctrineCriteriaBuilderFactory,
            $uidMapperPlatform,
            Media::class
        );
    }
}
