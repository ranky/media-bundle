<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\DeleteMedia;

use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Domain\Event\DomainEventPublisher;

class DeleteMedia
{

    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly FileRepositoryInterface $fileRepository,
        private readonly DomainEventPublisher $domainEventPublisher
    ) {
    }

    
    public function __invoke(string $id): void
    {
        $media    = $this->mediaRepository->getById(MediaId::fromString($id));
        $fileName = $media->file()->name();
        /* record events */
        $media->delete();
        /* Delete from DB */
        $this->mediaRepository->delete($media);
        /* Delete from filesystem */
        $this->fileRepository->delete($fileName);
        /* raised events */
        $this->domainEventPublisher->publish(...$media->recordedEvents());
    }


}
