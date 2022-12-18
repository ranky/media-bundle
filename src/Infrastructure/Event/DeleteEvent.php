<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Event;


use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Symfony\Contracts\EventDispatcher\Event;

class DeleteEvent extends Event
{
    public const PRE_DELETE = 'ranky.media.pre_delete';
    public const POST_DELETE = 'ranky.media.post_delete';

    protected MediaId $mediaId;

    public function __construct(MediaId $mediaId)
    {
        $this->mediaId = $mediaId;
    }

    public function getMediaId(): MediaId
    {
        return $this->mediaId;
    }
}
