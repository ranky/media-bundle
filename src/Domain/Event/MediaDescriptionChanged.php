<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Event;

use Ranky\SharedBundle\Domain\Event\AbstractDomainEvent;

/**
 * @phpstan-import-type DescriptionArray from \Ranky\MediaBundle\Domain\ValueObject\Description
 * @property array{ description: DescriptionArray } $payload
 * @method array{ description: DescriptionArray } payload()
 */
final class MediaDescriptionChanged extends AbstractDomainEvent
{
    /**
     * @return DescriptionArray
     */
    public function description(): array
    {
        return $this->payload()['description'];
    }

}
