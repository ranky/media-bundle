<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Dbal\Types;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnail;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnails;
use Ranky\MediaBundle\Infrastructure\Persistence\Dbal\Types\ThumbnailCollectionType;

class ThumbnailCollectionTypeTest extends TestCase
{
    /**
     * @throws \Doctrine\DBAL\Types\ConversionException
     * @throws \JsonException
     */
    public function testItShouldGetThumbnailCollectionType(): void
    {
        $thumbnailCollectionType = new ThumbnailCollectionType();
        $this->assertSame(
            'LONGTEXT',
            $thumbnailCollectionType->getSQLDeclaration([], new MySqlPlatform())
        );

        $data                = [
            'breakpoint' => Breakpoint::LARGE->value,
            'name'       => 'image.jpg',
            'path'       => 'image.jpg',
            'size'       => 200,
            'width'      => 300,
            'height'     => 200,
        ];
        $thumbnail           = Thumbnail::fromArray($data);
        $thumbnailCollection = new Thumbnails([$thumbnail]);

        $this->assertEquals(
            $thumbnailCollection,
            $thumbnailCollectionType->convertToPHPValue(
                \json_encode([$data], \JSON_THROW_ON_ERROR),
                new MySqlPlatform()
            )
        );

        $this->assertSame(
            \json_encode([$data], \JSON_THROW_ON_ERROR),
            $thumbnailCollectionType->convertToDatabaseValue($thumbnailCollection, new MySqlPlatform())
        );
    }
}
