<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Filter\Mysql  ;


use Doctrine\ORM\QueryBuilder;
use Ranky\MediaBundle\Domain\Criteria\MediaCriteria;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Filter\DoctrineYearMonthDateFilterExtensionVisitor;
use Ranky\MediaBundle\Tests\Dummy\Media\Domain\Media;
use Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Mysql\BaseDbMysqlTestCase;
use Ranky\SharedBundle\Filter\ConditionOperator;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\CriteriaBuilder\DoctrineCriteriaBuilder;
use Ranky\SharedBundle\Filter\Visitor\Extension\FilterExtensionVisitorFacade;

class DoctrineYearMonthDateFilterTest extends BaseDbMysqlTestCase
{

    /**
     * @throws \Doctrine\ORM\Exception\MissingMappingDriverImplementation
     * @throws \Doctrine\DBAL\Exception
     */
    public function testItShouldCreateYearMonthDateFilterWithDoctrineDriver(): void
    {
        $this->assertTrue(true);
        $filter        = new ConditionFilter('m.createdAt', ConditionOperator::EQUALS, '2022-10');
        $mediaCriteria = MediaCriteria::default();
        $mediaCriteria->addFilter($filter);

        $queryBuilder = new QueryBuilder(self::getEntityManager());
        $queryBuilder
            ->from(Media::class, 'm')
            ->select('m.createdAt');
        $doctrineCriteriaBuilder = new DoctrineCriteriaBuilder(
            $queryBuilder,
            $mediaCriteria,
            [new FilterExtensionVisitorFacade(new DoctrineYearMonthDateFilterExtensionVisitor())]
        );


        $this->assertSame(
            'SELECT r0_.created_at AS created_at_0 FROM ranky_media r0_ WHERE YEAR(r0_.created_at) = ? AND MONTH(r0_.created_at) = ?',
            $doctrineCriteriaBuilder->where()->getQuery()->getSQL()
        );
    }
}
