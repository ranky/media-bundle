<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\CreateMedia;

use Ranky\MediaBundle\Application\FileManipulation\CompressFile\CompressFile;
use Ranky\MediaBundle\Domain\Event\MediaCreated;
use Ranky\SharedBundle\Domain\Event\DomainEventSubscriber;

class CompressFileOnMediaCreated implements DomainEventSubscriber
{

    public function __construct(
        private readonly CompressFile $compressFile
    ) {
    }

    public static function subscribedTo(): string
    {
        return MediaCreated::class;
    }


    public function __invoke(MediaCreated $mediaCreated): void
    {
        $this->compressFile->__invoke($mediaCreated->aggregateId());
    }

    public static function priority(): int
    {
        return 1;
    }
}
