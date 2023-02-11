<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\CreateMedia;

use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Event\MediaCreated;
use Ranky\MediaBundle\Domain\Service\GenerateThumbnailsHandler;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Domain\Event\DomainEventSubscriber;

class GenerateThumbnailsOnMediaCreated implements DomainEventSubscriber
{

    public function __construct(
        private readonly GenerateThumbnailsHandler $generateThumbnailsHandler,
        private readonly MediaRepository $mediaRepository
    ) {
    }

    public static function subscribedTo(): string
    {
        return MediaCreated::class;
    }

    public static function priority(): int
    {
        return 2;
    }


    public function __invoke(MediaCreated $mediaCreated): void
    {
        $media = $this->mediaRepository->getById(MediaId::fromString($mediaCreated->aggregateId()));
        $this->generateThumbnailsHandler->generate(
            $mediaCreated->aggregateId(),
            $media->file(),
            $media->dimension()
        );
    }
}
