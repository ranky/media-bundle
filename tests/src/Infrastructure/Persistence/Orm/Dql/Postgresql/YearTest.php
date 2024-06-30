<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Postgresql;

use Ranky\MediaBundle\Tests\Dummy\Media\Domain\Media;

class YearTest extends BaseDbPostgresqlTestCase
{

    /**
     * @throws \Doctrine\DBAL\Exception|\Doctrine\ORM\Exception\MissingMappingDriverImplementation
     */
    public function testItShouldGetYearQuery(): void
    {
        $em    = self::getEntityManager();
        $query = $em->createQuery('SELECT YEAR(m.createdAt) from '.Media::class.' m');

        $this->assertSame(
            'SELECT EXTRACT(YEAR FROM r0_.created_at) AS sclr_0 FROM ranky_media r0_',
            $query->getSQL()
        );
    }
}
