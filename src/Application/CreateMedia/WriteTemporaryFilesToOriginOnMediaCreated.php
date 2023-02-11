<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\CreateMedia;

use Ranky\MediaBundle\Application\FileManipulation\WriteFile\WriteTemporaryFileToOrigin;
use Ranky\MediaBundle\Domain\Event\MediaCreated;
use Ranky\SharedBundle\Domain\Event\DomainEventSubscriber;

class WriteTemporaryFilesToOriginOnMediaCreated implements DomainEventSubscriber
{

    public function __construct(
        private readonly WriteTemporaryFileToOrigin $writeTemporaryFileToOrigin
    ) {
    }

    public static function subscribedTo(): string
    {
        return MediaCreated::class;
    }

    public static function priority(): int
    {
        return 0;
    }


    public function __invoke(MediaCreated $mediaCreated): void
    {
        $this->writeTemporaryFileToOrigin->__invoke($mediaCreated->aggregateId());
    }
}
