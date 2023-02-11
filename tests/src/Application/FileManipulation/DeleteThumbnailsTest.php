<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\FileManipulation;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\FileManipulation\DeleteFile\DeleteThumbnails;
use Ranky\MediaBundle\Domain\Contract\FileRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\MediaBundle\Tests\Domain\ThumbnailsFactory;

class DeleteThumbnailsTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testItShouldDeleteThumbnails(): void
    {
        $media           = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $thumbnails      = ThumbnailsFactory::make($media);
        $thumbnailsArray = $thumbnails->toArray();

        $consecutiveParameters = array_reduce(
            $thumbnailsArray,
            static function ($thumbnails, $thumbnail) {
                $thumbnails[] = [$thumbnail['path']];

                return $thumbnails;
            },
            []
        );

        $fileRepository = $this->createMock(FileRepository::class);
        $fileRepository
            ->expects($this->exactly(count($thumbnailsArray)))
            ->method('delete')
            ->withConsecutive(...$consecutiveParameters);
        $deleteThumbnails = new DeleteThumbnails($fileRepository);
        $deleteThumbnails->delete($thumbnailsArray);
    }
}
