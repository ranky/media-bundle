<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Event;

use Ranky\SharedBundle\Domain\Event\AbstractDomainEvent;

/**
 * @property array{ name: string, thumbnails: array } $payload
 * @method array{ name: string, thumbnails: array } payload()
 */
final class MediaThumbnailsUpdated extends AbstractDomainEvent
{

}
