<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\DeleteMedia;

use Ranky\MediaBundle\Domain\Contract\FileRepository;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Domain\Event\DomainEventPublisher;

class DeleteMedia
{

    public function __construct(
        private readonly MediaRepository $mediaRepository,
        private readonly FileRepository $fileRepository,
        private readonly DomainEventPublisher $domainEventPublisher
    ) {
    }


    public function __invoke(string $id): void
    {
        $media    = $this->mediaRepository->getById(MediaId::fromString($id));
        $fileName = $media->file()->name();
        /* Delete from DB */
        $this->mediaRepository->delete($media);
        /* Delete from filesystem */
        $this->fileRepository->delete($fileName);
        /* raised events */
        $this->domainEventPublisher->publish(...$media->recordedEvents());
    }


}
