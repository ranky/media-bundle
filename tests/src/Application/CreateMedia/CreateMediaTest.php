<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\CreateMedia;

use Ranky\MediaBundle\Application\CreateMedia\CreateMedia;
use Ranky\MediaBundle\Application\CreateMedia\FileCompressOnMediaCreated;
use Ranky\MediaBundle\Application\CreateMedia\GenerateThumbnailsOnMediaCreated;
use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Domain\Event\InMemoryDomainEventPublisher;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

class CreateMediaTest extends BaseUnitTestCase
{

    public function testItShouldCreateMedia(): MediaResponse
    {
        /** Dummy data */
        $media               = MediaFactory::random(MimeType::IMAGE, 'png');
        $uploadedFileRequest = new UploadedFileRequest(
            $media->file()->path(),
            $media->file()->name(),
            $media->file()->mime(),
            $media->file()->extension(),
            $media->file()->size()
        );
        $userIdentifier      = UserIdentifier::fromString('jcarlos');

        /* Mock repositories */
        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $mediaRepository
            ->expects($this->once())
            ->method('save');

        $mediaRepository
            ->expects($this->once())
            ->method('nextIdentity')
            ->willReturn($media->id());

        $fileRepository = $this->createMock(FileRepositoryInterface::class);
        $fileRepository
            ->expects($this->once())
            ->method('upload')
            ->with($uploadedFileRequest)
            ->willReturn($media->file());

        $filePathResolver = $this->createMock(FilePathResolverInterface::class);
        $filePathResolver
            ->expects($this->once())
            ->method('resolve')
            ->willReturn(sys_get_temp_dir().'/ranky_media_bundle_test/upload');

        /* Mock domain event subscribers */
        $fileCompressOnMediaCreated = $this->getMockBuilder(FileCompressOnMediaCreated::class)
            ->onlyMethods(['__invoke'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileCompressOnMediaCreated
            ->expects($this->once())
            ->method('__invoke');

        $generateThumbnailsOnMediaCreated = $this->getMockBuilder(GenerateThumbnailsOnMediaCreated::class)
            ->onlyMethods(['__invoke'])
            ->disableOriginalConstructor()
            ->getMock();
        $generateThumbnailsOnMediaCreated
            ->expects($this->once())
            ->method('__invoke');

        $domainEventPublisher = new InMemoryDomainEventPublisher(
            new \ArrayIterator([$fileCompressOnMediaCreated, $generateThumbnailsOnMediaCreated])
        );

        $responseTransformer = $this->getMediaTransformer($userIdentifier);

        $createMedia = new CreateMedia(
            $mediaRepository,
            $fileRepository,
            $filePathResolver,
            $domainEventPublisher,
            $responseTransformer
        );

        return $createMedia->__invoke($uploadedFileRequest, $userIdentifier->value());
    }

}
