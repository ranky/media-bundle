<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Domain;

use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\Description;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Common\FileHelper;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

final class MediaFactory
{

    public const DEFAULT_USER_IDENTIFIER = 'jcarlos';

    public static function create(
        MediaId $id,
        File $file,
        Dimension $dimension,
        ?UserIdentifier $userIdentifier = null,
        ?Description $description = null
    ): Media {
        $userIdentifier ??= UserIdentifier::fromString(self::DEFAULT_USER_IDENTIFIER);
        $description    ??= new Description(FileHelper::humanTitleFromFileName($file->name()));

        return Media::create($id, $file, $userIdentifier, $dimension, $description);
    }

    public static function random(
        MimeType $mimeType,
        ?string $extension = null,
        ?MediaId $mediaId = null,
        UserIdentifier $userIdentifier = null
    ): Media {
        $userIdentifier ??= UserIdentifier::fromString(self::DEFAULT_USER_IDENTIFIER);
        $mediaId        ??= MediaId::create();
        if ($extension !== null) {
            $files = self::filesByExtension($mimeType, $extension);
        } else {
            $files = self::filesByMimeType($mimeType);
        }

        if (($countFiles = \count($files)) === 0) {
            throw new \LengthException(
                sprintf(
                    '$files array is empty with mimeType <%s> and extension <%s>',
                    $mimeType->value,
                    $extension ?? 'random'
                )
            );
        }

        $file        = $files[\random_int(0, $countFiles - 1)];
        $dimension   = self::getDimensionByFileName($file->name());
        $description = new Description(FileHelper::humanTitleFromFileName($file->name()));

        return self::create($mediaId, $file, $dimension, $userIdentifier, $description);
    }

    /**
     * @param MimeType $mimeType
     * @return File[]
     */
    public static function filesByMimeType(MimeType $mimeType): array
    {
        return match ($mimeType) {
            MimeType::IMAGE => [
                new File('Gans-of-London.jpg', 'Gans-of-London.jpg', 'image/jpeg', 'jpg', 350168),
                new File('good-luck.gif', 'good-luck.gif', 'image/gif', 'gif', 2273559),
                new File('SamplePNGImage_500kbmb.png', 'SamplePNGImage_500kbmb.png', 'image/png', 'png', 503111),
                new File('file_example_WEBP_250kB.webp', 'file_example_WEBP_250kB.webp', 'image/webp', 'webp', 257750),
            ],
            MimeType::APPLICATION => [
                new File('dummy.pdf', 'dummy.pdf', 'application/pdf', 'pdf', 13264),
            ],
            MimeType::TEXT => [
                new File('dummy.txt', 'dummy.txt', 'text/plain', 'txt', 9),
            ],
            MimeType::AUDIO => [
                new File('SampleAudio_0.4mb.mp3', 'SampleAudio_0.4mb.mp3', 'audio/mpeg', 'mp3', 443926),
            ],
            MimeType::VIDEO => [
                new File('SampleVideo_1280x720_2mb.mp4', 'SampleVideo_1280x720_2mb.mp4', 'video/mp4', 'mp4', 2107842),
            ],
        };
    }

    /**
     * @param \Ranky\MediaBundle\Domain\Enum\MimeType $mimeType
     * @param string $extension
     * @return array<int, File>
     */
    public static function filesByExtension(MimeType $mimeType, string $extension): array
    {
        $filesByMimeType = self::filesByMimeType($mimeType);

        return \array_values(
            \array_filter($filesByMimeType, static fn(File $file) => $file->extension() === $extension)
        );
    }

    public static function getDimensionByFileName(string $fileName): Dimension
    {
        return match ($fileName) {
            'friends.jpg' => Dimension::fromArray([720, 377]),
            'Gans-of-London.jpg' => Dimension::fromArray([1200, 1800]),
            'good-luck.gif' => Dimension::fromArray([750, 399]),
            default => new Dimension(null, null)
        };
    }

}
