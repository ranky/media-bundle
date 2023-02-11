<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\CreateMedia;

use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\SafeFileName\SafeFileName;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Contract\TemporaryFileRepository;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\Description;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Common\FileHelper;
use Ranky\SharedBundle\Domain\Event\DomainEventPublisher;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

class CreateMedia
{
    public function __construct(
        private readonly SafeFileName $safeFileName,
        private readonly MediaRepository $mediaRepository,
        private readonly TemporaryFileRepository $temporaryFileRepository,
        private readonly DomainEventPublisher $domainEventPublisher,
        private readonly MediaToResponseTransformer $responseTransformer
    ) {
    }

    public function __invoke(
        UploadedFileRequest $uploadedFileRequest,
        ?string $userIdentifier = null,
        ?string $mediaId = null
    ): MediaResponse {
        // value objects
        $id               = $mediaId ? MediaId::fromString($mediaId) : $this->mediaRepository->nextIdentity();
        $userIdentifierVO = new UserIdentifier($userIdentifier);
        $fileName         = $this->safeFileName->__invoke(
            $uploadedFileRequest->name(),
            $uploadedFileRequest->extension()
        );
        $file             = new File(
            $fileName,
            $fileName,
            $uploadedFileRequest->mime(),
            $uploadedFileRequest->extension(),
            $uploadedFileRequest->size()
        );

        $dimension   = $this->temporaryFileRepository->dimension($uploadedFileRequest->path(), $file->mime());
        $description = new Description(FileHelper::humanTitleFromFileName($file->name()));
        // prepare temporary file for processing
        $temporaryFile = $this->temporaryFileRepository->temporaryFile($file->path());
        $this->temporaryFileRepository->copy($uploadedFileRequest->path(), $temporaryFile);
        // create and save media
        $media = Media::create(
            $id,
            $file,
            $userIdentifierVO,
            $dimension,
            $description
        );
        $this->mediaRepository->save($media);

        // publish domain events
        $this->domainEventPublisher->publish(...$media->recordedEvents());

        return $this->responseTransformer->mediaToResponse($media);
    }
}
