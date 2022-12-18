<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql;

use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;

class SubstringIndexTest extends BaseIntegrationTestCase
{

    public function testItShouldGetSubstringIndexQuery(): void
    {
        $em    = self::getDoctrineManager();
        $query = $em->createQuery('SELECT SUBSTRING_INDEX(m.file.mime,\'/\',1) from '.Media::class.' m');

        $this->assertSame(
            'SELECT SUBSTRING_INDEX(r0_.mime, \'/\', 1) AS sclr_0 FROM ranky_media r0_',
            $query->getSQL()
        );
    }
}
