<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Enum;

enum GifResizeDriver: string
{
    case NONE = 'none';
    case FFMPEG = 'ffmpeg';
    case GIFSICLE = 'gifsicle';
    case IMAGICK = 'imagick';

    /**
     * @return string[]
     */
    public static function drivers(): array
    {
        return \array_map(static fn(self $gifResize) => $gifResize->value, self::cases());
    }

}
