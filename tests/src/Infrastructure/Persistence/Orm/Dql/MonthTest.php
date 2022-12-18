<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql;

use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;

class MonthTest extends BaseIntegrationTestCase
{

    public function testItShouldGetMonthQuery(): void
    {
        $em    = self::getDoctrineManager();
        $query = $em->createQuery('SELECT MONTH(m.createdAt) from '.Media::class.' m');

        $this->assertSame(
            'SELECT MONTH(r0_.created_at) AS sclr_0 FROM ranky_media r0_',
            $query->getSQL()
        );
    }
}
