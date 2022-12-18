<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Event;

use Ranky\SharedBundle\Domain\Event\AbstractDomainEvent;

/**
 * @property array{ dimension: array } $payload
 * @method array{ dimension: array } payload()
 */
final class MediaDimensionUpdated extends AbstractDomainEvent
{

}
