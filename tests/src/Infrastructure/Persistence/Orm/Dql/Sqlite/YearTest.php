<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Sqlite;

use Ranky\MediaBundle\Domain\Model\Media;

class YearTest extends BaseDbSqliteTestCase
{

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function testItShouldGetYearQuery(): void
    {
        $em    = static::createEntityManager();
        $query = $em->createQuery('SELECT YEAR(m.createdAt) from '.Media::class.' m');

        $this->assertSame(
            "SELECT STRFTIME('%Y', r0_.created_at) AS sclr_0 FROM ranky_media r0_",
            $query->getSQL()
        );
    }
}
