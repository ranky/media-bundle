<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Dbal\Types;


use Ranky\MediaBundle\Domain\ValueObject\Thumbnails;
use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\JsonCollectionType;

/**
 * @template-extends JsonCollectionType<Thumbnails>
 */
final class ThumbnailCollectionType extends JsonCollectionType
{
    public const NAME = 'thumbnail_collection';

    protected function fieldName(): string
    {
        return self::NAME;
    }

    protected function collectionClass(): string
    {
        return Thumbnails::class;
    }

}
