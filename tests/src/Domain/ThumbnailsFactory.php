<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Domain;

use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnail;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnails;

class ThumbnailsFactory
{
    public static function make(Media $media): Thumbnails
    {
        return self::generate($media, $media->file()->name());
    }

    public static function withNewFileName(Media $media, string $newFileName): Thumbnails
    {
       return self::generate($media, $newFileName);
    }

    private static function generate(Media $media, string $fileName): Thumbnails
    {
        $file       = $media->file();
        $dimension  = $media->dimension();
        $thumbnails = new Thumbnails();

        foreach (Breakpoint::cases() as $breakpoint) {
            if ($breakpoint->dimensions()[0] > $dimension->width()) {
                continue;
            }

            $thumbnails->add(
                new Thumbnail(
                    $breakpoint->value,
                    $fileName,
                    '/'.$breakpoint->value.'/'.$fileName,
                    $file->size(),
                    new Dimension($breakpoint->dimensions()[0], $breakpoint->dimensions()[1] ?? null)
                )
            );
        }

        return $thumbnails;
    }

}
