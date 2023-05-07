<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Mysql;

use Ranky\MediaBundle\Domain\Model\Media;

class MimeTypeTest extends BaseDbMysqlTestCase
{
    /**
     * @throws \Doctrine\ORM\Exception\MissingMappingDriverImplementation
     * @throws \Doctrine\DBAL\Exception
     */
    public function testItShouldGetMimeTypeQuery(): void
    {
        $em    = self::getEntityManager();
        $query = $em->createQuery('SELECT MIME_TYPE(m.file.mime) from '.Media::class.' m');

        $this->assertSame(
            'SELECT SUBSTR(r0_.mime, 1, INSTR(r0_.mime,"/")-1) AS sclr_0 FROM ranky_media r0_',
            $query->getSQL()
        );
    }
}
