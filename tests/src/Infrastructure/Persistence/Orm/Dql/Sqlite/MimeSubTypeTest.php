<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Sqlite;

use Ranky\MediaBundle\Domain\Model\Media;

class MimeSubTypeTest extends BaseDbSqliteTestCase
{

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function testItShouldGetMimeTypeQuery(): void
    {
        $em    = self::createEntityManager();
        $query = $em->createQuery('SELECT MIME_SUBTYPE(m.file.mime) from '.Media::class.' m');

        $this->assertSame(
            'SELECT SUBSTR(r0_.mime, INSTR(r0_.mime, "/") + 1, LENGTH(r0_.mime)) AS sclr_0 FROM ranky_media r0_',
            $query->getSQL()
        );
    }
}
