<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\CreateMedia;

use Ranky\MediaBundle\Application\FileManipulation\DeleteFile\DeleteTemporaryFiles;
use Ranky\MediaBundle\Domain\Event\MediaCreated;
use Ranky\SharedBundle\Domain\Event\DomainEventSubscriber;

class DeleteTemporaryFilesOnMediaCreated implements DomainEventSubscriber
{
    public function __construct(
        private readonly DeleteTemporaryFiles $deleteTemporaryFiles
    ) {
    }

    public static function subscribedTo(): string
    {
        return MediaCreated::class;
    }

    public static function priority(): int
    {
        return -1;
    }


    public function __invoke(MediaCreated $mediaCreated): void
    {
        $this->deleteTemporaryFiles->__invoke($mediaCreated->aggregateId());
    }
}
