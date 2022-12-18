<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Enum;

enum MimeType: string
{

    case APPLICATION = 'application';
    case AUDIO = 'audio';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case TEXT = 'text';

    public static function fromMime(mixed $value): MimeType
    {
        if (\str_contains($value, '/')){
            $value = \explode('/', $value)[0];
        }

        return self::from($value);

    }

    /**
     * @return string[]
     */
    public static function types(): array
    {
        return \array_map(static fn(self $mimeType) => $mimeType->value, self::cases());
    }

}
