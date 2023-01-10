<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Postgresql;

use Ranky\MediaBundle\Domain\Model\Media;

class MimeSubTypeTest extends BaseDbPostgresqlTestCase
{

    /**
     * @throws \Doctrine\DBAL\Exception|\Doctrine\ORM\Exception\MissingMappingDriverImplementation
     */
    public function testItShouldGetMimeSubTypeQuery(): void
    {
        $em    = self::getEntityManager();
        $query = $em->createQuery('SELECT MIME_SUBTYPE(m.file.mime) from '.Media::class.' m');

        $this->assertSame(
            'SELECT SUBSTRING(r0_.mime, POSITION("/" in r0_.mime)+1, LENGTH(r0_.mime)) AS sclr_0 FROM ranky_media r0_',
            $query->getSQL()
        );
    }
}
