<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Criteria;

use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Filter\Criteria;

class MediaCriteria extends Criteria
{
    public const MODEL_ALIAS              = 'm';
    public const DEFAULT_PAGINATION_LIMIT = 30;
    public const DEFAULT_PAGINATION_RANGE = 2;
    public const DEFAULT_ORDER_FIELD      = 'createdAt';
    public const DEFAULT_ORDER_DIRECTION  = 'DESC';

    public static function normalizeNameFields(): array
    {
        return [
            'id'        => 'm.id',
            'mime'      => 'm.file.mime',
            'name'      => 'm.file.name',
            'createdAt' => 'm.createdAt',
            'createdBy' => 'm.createdBy',
        ];
    }

    public static function normalizeValues(): array
    {
        return [
            'id' => static function (mixed $value) {
                if ($value instanceof MediaId) {
                    return $value->asBinary();
                }
                if (\str_contains($value, ',')) {
                    return \array_map(
                        static fn (string $mediaId) => MediaId::fromString($mediaId)->asBinary(),
                        \explode(',', $value)
                    );
                }

                return MediaId::fromString($value)->asBinary();
            },
        ];
    }

    public static function modelClass(): string
    {
        return Media::class;
    }

    public static function modelAlias(): string
    {
        return self::MODEL_ALIAS;
    }
}
