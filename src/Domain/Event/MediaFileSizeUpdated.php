<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Event;

use Ranky\SharedBundle\Domain\Event\AbstractDomainEvent;

/**
 * @property array{ oldSize: int, newSize: int } $payload
 * @method array{ oldSize: int, newSize: int } payload()
 */
final class MediaFileSizeUpdated extends AbstractDomainEvent
{

}
