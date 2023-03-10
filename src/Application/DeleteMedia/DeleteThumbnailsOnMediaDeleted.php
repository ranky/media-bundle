<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\DeleteMedia;

use Ranky\MediaBundle\Application\FileManipulation\Thumbnails\DeleteThumbnails\DeleteThumbnails;
use Ranky\MediaBundle\Domain\Event\MediaDeleted;
use Ranky\SharedBundle\Domain\Event\DomainEvent;
use Ranky\SharedBundle\Domain\Event\DomainEventSubscriber;

class DeleteThumbnailsOnMediaDeleted implements DomainEventSubscriber
{


    public function __construct(private readonly DeleteThumbnails $deleteThumbnails)
    {
    }

    public static function subscribedTo(): string
    {
        return MediaDeleted::class;
    }

    public function __invoke(DomainEvent $domainEvent): void
    {
        $this->deleteThumbnails->delete($domainEvent->payload()['thumbnails']);
    }

    public static function priority(): int
    {
        return 0;
    }
}
