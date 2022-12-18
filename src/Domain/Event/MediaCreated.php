<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Event;

use Ranky\SharedBundle\Domain\Event\AbstractDomainEvent;

/**
 * @property array{ name: string } $payload
 * @method array{ name: string } payload()
 */
final class MediaCreated extends AbstractDomainEvent
{

}
