<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\FileManipulation;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\FileManipulation\RenameFile\RenameThumbnails;
use Ranky\MediaBundle\Domain\Contract\FileRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\MediaBundle\Tests\Domain\ThumbnailsFactory;

class RenameThumbnailsTest extends TestCase
{
    public function testItShouldRenameThumbnails(): void
    {
        $media           = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $thumbnails      = ThumbnailsFactory::make($media);
        $thumbnailsArray = $thumbnails->toArray();
        $newFileName     = 'rename.'.$media->file()->extension();

        $consecutiveParameters = array_reduce(
            $thumbnailsArray,
            static function ($thumbnails, $thumbnail) use ($newFileName) {
                $thumbnails[] = [
                    '/'.$thumbnail['breakpoint'].'/'.$thumbnail['name'],
                    '/'.$thumbnail['breakpoint'].'/'.$newFileName,
                ];

                return $thumbnails;
            },
            []
        );
        $fileRepository        = $this->createMock(FileRepository::class);
        $fileRepository
            ->expects($this->exactly(\count($thumbnailsArray)))
            ->method('rename')
            ->withConsecutive(...$consecutiveParameters);



        $renameThumbnails = new RenameThumbnails($fileRepository);
        $renameThumbnails->__invoke($thumbnails, $newFileName);
    }
}
