<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Event;

use Ranky\SharedBundle\Domain\Event\AbstractDomainEvent;

/**
 * @property array{ size: int } $payload
 * @method array{ size: int } payload()
 */
final class MediaFileSizeChanged extends AbstractDomainEvent
{
        public function size(): int
        {
            return $this->payload()['size'];
        }

}
