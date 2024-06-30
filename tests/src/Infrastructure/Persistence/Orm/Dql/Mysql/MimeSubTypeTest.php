<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Mysql;

use Ranky\MediaBundle\Tests\Dummy\Media\Domain\Media;

class MimeSubTypeTest extends BaseDbMysqlTestCase
{
    /**
     * @throws \Doctrine\ORM\Exception\MissingMappingDriverImplementation
     * @throws \Doctrine\DBAL\Exception
     */
    public function testItShouldGetMimeSubTypeQuery(): void
    {
        $em    = self::getEntityManager();
        $query = $em->createQuery('SELECT MIME_SUBTYPE(m.file.mime) from '.Media::class.' m');

        $this->assertSame(
            'SELECT SUBSTR(r0_.mime, INSTR(r0_.mime, "/") + 1, LENGTH(r0_.mime)) AS sclr_0 FROM ranky_media r0_',
            $query->getSQL()
        );
    }
}
