<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Enum;

enum ImageResizeDriver: string
{

    case GD = 'gd';
    case IMAGICK = 'imagick';

    /**
     * @return string[]
     */
    public function supportedFormats(): array
    {
        return match ($this) {
            self::GD => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'],
            self::IMAGICK => ['jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff', 'bmp', 'ico', 'psd', 'webp'],
        };
    }

    /**
     * @return string[]
     */
    public static function drivers(): array
    {
        return \array_map(static fn(self $driver) => $driver->value, self::cases());
    }

    public static function default(): self
    {
        return self::IMAGICK;
    }
}
