<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Dbal\Types;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;

class MediaIdType extends TestCase
{
    /**
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function testItShouldGetMediaIdType(): void
    {
        $mediaIdType = new \Ranky\MediaBundle\Infrastructure\Persistence\Dbal\Types\MediaIdType();

        $this->assertSame(
            'BINARY(16)',
            $mediaIdType->getSQLDeclaration([], new MySqlPlatform())
        );

        $mediaId = MediaId::generate();
        $this->assertEquals(
            $mediaId,
            $mediaIdType->convertToPHPValue($mediaId->asString(), new MySqlPlatform())
        );

        $this->assertEquals(
            $mediaId->asBinary(),
            $mediaIdType->convertToDatabaseValue($mediaId, new MySqlPlatform())
        );
    }
}
