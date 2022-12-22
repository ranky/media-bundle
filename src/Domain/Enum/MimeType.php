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
     * Supported image types by the browser
     * https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Image_types#common_image_file_types
     * @return string[]
     */
    public static function supportedImageTypes(): array
    {
        return [
            'apng',
            'avif',
            'gif',
            'jpeg',
            'png',
            'svg+xml',
            'webp',
            'x-icon',
            'vnd.microsoft.icon',
            'bmp',
            'x-ms-bmp'
        ];
    }
    /**
     * Placeholder for unsupported image types by the browser
     * https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Image_types#common_image_file_types
     *
     * @return array<string, string>
     */
    public static function imagesTypesWithPlaceholder(): array
    {
        return [
        /*  'x-icon' => 'images/placeholder/ico.jpg',
            'vnd.microsoft.icon' => 'images/placeholder/ico.jpg',
            'bmp' => 'images/placeholder/bmp.jpg',
            'x-ms-bmp' => 'images/placeholder/bmp.jpg',*/
            'tiff' => 'images/placeholder/tiff.jpg',
            'vnd.adobe.photoshop' => 'images/placeholder/psd.jpg'
        ];
    }


    /**
     * @return string[]
     */
    public static function types(): array
    {
        return \array_map(static fn(self $mimeType) => $mimeType->value, self::cases());
    }

}
