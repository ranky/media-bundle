<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Postgresql;

use Ranky\MediaBundle\Tests\Dummy\Media\Domain\Media;

class MimeTypeTest extends BaseDbPostgresqlTestCase
{

    /**
     * @throws \Doctrine\DBAL\Exception|\Doctrine\ORM\Exception\MissingMappingDriverImplementation
     */
    public function testItShouldGetMimeTypeQuery(): void
    {
        $em    = self::getEntityManager();
        $query = $em->createQuery('SELECT MIME_TYPE(m.file.mime) from '.Media::class.' m');

        $this->assertSame(
            'SELECT SUBSTRING(r0_.mime, 1, POSITION(\'/\' in r0_.mime)-1) AS sclr_0 FROM ranky_media r0_',
            $query->getSQL()
        );
    }
}
