<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Event;

use Ranky\SharedBundle\Domain\Event\AbstractDomainEvent;

/**
 * @phpstan-import-type DimensionArray from \Ranky\MediaBundle\Domain\ValueObject\Dimension
 * @property array{ dimension: DimensionArray } $payload
 * @method array{ dimension: DimensionArray } payload()
 */
final class MediaDimensionChanged extends AbstractDomainEvent
{
    /**
     * @return DimensionArray
     */
    public function dimension(): array
    {
        return $this->payload()['dimension'];
    }

}
