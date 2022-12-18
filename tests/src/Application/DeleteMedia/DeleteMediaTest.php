<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\DeleteMedia;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\DeleteMedia\DeleteMedia;
use Ranky\MediaBundle\Application\DeleteMedia\DeleteThumbnailsOnMediaDeleted;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalFileRepository;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Domain\Event\InMemoryDomainEventPublisher;

class DeleteMediaTest extends TestCase
{

    public function testItShouldDeleteMedia(): void
    {
        /** Dummy data */
        $media = MediaFactory::random(MimeType::IMAGE, 'jpg');

        /* Mock Repositories*/
        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $mediaRepository
            ->expects($this->once())
            ->method('delete');

        $mediaRepository
            ->expects($this->once())
            ->method('getById')
            ->with($media->id())
            ->willReturn($media);

        $fileRepository = $this->createMock(LocalFileRepository::class);
        $fileRepository
            ->expects($this->once())
            ->method('delete')
            ->with($media->file()->name());

        /* Mock domain event subscribers */
        $deleteThumbnailsOnMediaDeleted = $this->getMockBuilder(DeleteThumbnailsOnMediaDeleted::class)
            ->onlyMethods(['__invoke'])
            ->disableOriginalConstructor()
            ->getMock();
        $deleteThumbnailsOnMediaDeleted
            ->expects($this->once())
            ->method('__invoke');

        $domainEventPublisher = new InMemoryDomainEventPublisher(
            new \ArrayIterator([$deleteThumbnailsOnMediaDeleted])
        );

        $deleteMedia = new DeleteMedia($mediaRepository, $fileRepository, $domainEventPublisher);
        $deleteMedia->__invoke((string)$media->id());
    }
}
