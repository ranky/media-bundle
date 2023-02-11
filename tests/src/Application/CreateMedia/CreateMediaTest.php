<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\CreateMedia;

use Ranky\MediaBundle\Application\CreateMedia\CreateMedia;
use Ranky\MediaBundle\Application\CreateMedia\CompressFileOnMediaCreated;
use Ranky\MediaBundle\Application\CreateMedia\GenerateThumbnailsOnMediaCreated;
use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\SafeFileName\SafeFileName;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
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
        $file = $media->file();
        $uploadedFileRequest = new UploadedFileRequest(
            $file->path(),
            $file->name(),
            $file->mime(),
            $file->extension(),
            $file->size()
        );
        $userIdentifier      = UserIdentifier::fromString('jcarlos');

        $safeFileName = $this->createMock(SafeFileName::class);
        $safeFileName
            ->expects($this->once())
            ->method('__invoke')
            ->with($file->name(), $file->extension())
            ->willReturn($file->name());

        /* Mock repositories */
        $mediaRepository = $this->createMock(MediaRepository::class);
        $mediaRepository
            ->expects($this->once())
            ->method('save');

        $mediaRepository
            ->expects($this->once())
            ->method('nextIdentity')
            ->willReturn($media->id());


        $temporaryFileRepository = $this->getTemporaryFileRepository($file->path());
        $temporaryFileRepository
            ->expects($this->once())
            ->method('dimension')
            ->with($uploadedFileRequest->path(), $file->mime())
            ->willReturn($media->dimension());


        /* Mock domain event subscribers */
        $fileCompressOnMediaCreated = $this->getMockBuilder(CompressFileOnMediaCreated::class)
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
            new \ArrayIterator(
                [$fileCompressOnMediaCreated, $generateThumbnailsOnMediaCreated]
            )
        );

        $responseTransformer = $this->getMediaTransformer($userIdentifier);

        $createMedia = new CreateMedia(
            $safeFileName,
            $mediaRepository,
            $temporaryFileRepository,
            $domainEventPublisher,
            $responseTransformer
        );

        return $createMedia->__invoke($uploadedFileRequest, $userIdentifier->value());
    }

}
