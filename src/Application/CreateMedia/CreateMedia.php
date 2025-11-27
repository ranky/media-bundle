<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\CreateMedia;

use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\Description;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Common\FileHelper;
use Ranky\SharedBundle\Domain\Event\DomainEventPublisher;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

class CreateMedia
{

    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly FileRepositoryInterface $fileRepository,
        private readonly FilePathResolverInterface $filePathResolver,
        private readonly DomainEventPublisher $domainEventPublisher,
        private readonly MediaToResponseTransformer $responseTransformer,
        private readonly string $mediaEntity,
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
        // upload file
        $file = $this->fileRepository->upload($uploadedFileRequest);
        // create and save media
        $entityClass = $this->mediaEntity;
        /** @var Media $entityClass */
        $media = $entityClass::create(
            $id,
            $file,
            $userIdentifierVO,
            $this->fileRepository->dimensionsFromPath(
                $this->filePathResolver->resolve($file->path()),
                $file->mime()
            ),
            new Description(FileHelper::humanTitleFromFileName($file->name()))
        );
        $this->mediaRepository->save($media);
        // publish domain events
        $this->domainEventPublisher->publish(...$media->recordedEvents());

        return $this->responseTransformer->mediaToResponse($media);
    }


}
