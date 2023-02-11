<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\FileManipulation\RenameFile;

use Ranky\MediaBundle\Application\SafeFileName\SafeFileName;
use Ranky\MediaBundle\Application\UpdateMedia\UpdateMediaRequest;
use Ranky\MediaBundle\Domain\Contract\FileRepository;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Domain\Event\DomainEventPublisher;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

class RenameFile
{
    public function __construct(
        private readonly RenameThumbnails $renameThumbnails,
        private readonly MediaRepository $mediaRepository,
        private readonly FileRepository $fileRepository,
        private readonly SafeFileName $safeFileName,
        private readonly DomainEventPublisher $domainEventPublisher
    ) {
    }


    public function __invoke(UpdateMediaRequest $updateMediaRequest, ?string $userIdentifier): void
    {
        $media = $this->mediaRepository->getById(MediaId::fromString($updateMediaRequest->id()));

        if ($updateMediaRequest->name() === \pathinfo($media->file()->name(), \PATHINFO_FILENAME)) {
            return;
        }

        // get safe file name
        $newFileName = $this->safeFileName->__invoke($updateMediaRequest->name(), $media->file()->extension());
        $oldFileName = $media->file()->name();

        // rename original file
        $this->fileRepository->rename($oldFileName, $newFileName);
        $file = $media->file()->changeName($newFileName, $newFileName);
        $media->changeFile($file, new UserIdentifier($userIdentifier));

        // rename thumbnails file
        $thumbnails = $this->renameThumbnails->__invoke($media->thumbnails(), $newFileName);
        $media->changeThumbnails($thumbnails);

        $this->mediaRepository->save($media);
        $this->domainEventPublisher->publish(...$media->recordedEvents());
    }
}
