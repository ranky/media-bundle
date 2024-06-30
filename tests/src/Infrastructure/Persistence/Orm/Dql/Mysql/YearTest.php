<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Mysql;

use Ranky\MediaBundle\Tests\Dummy\Media\Domain\Media;

class YearTest extends BaseDbMysqlTestCase
{
    /**
     * @throws \Doctrine\ORM\Exception\MissingMappingDriverImplementation
     * @throws \Doctrine\DBAL\Exception
     */
    public function testItShouldGetYearQuery(): void
    {
        $em    = self::getEntityManager();
        $query = $em->createQuery('SELECT YEAR(m.createdAt) from '.Media::class.' m');

        $this->assertSame(
            'SELECT YEAR(r0_.created_at) AS sclr_0 FROM ranky_media r0_',
            $query->getSQL()
        );
    }
}
