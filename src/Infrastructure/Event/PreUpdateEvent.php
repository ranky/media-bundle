<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Event;


use Ranky\MediaBundle\Application\UpdateMedia\UpdateMediaRequest;
use Symfony\Contracts\EventDispatcher\Event;

class PreUpdateEvent extends Event
{
    public const NAME = 'ranky.media.pre_update';

    protected UpdateMediaRequest $updateMediaRequest;

    public function __construct(UpdateMediaRequest $updateMediaRequest)
    {
        $this->updateMediaRequest = $updateMediaRequest;
    }

    public function getUpdateMediaRequest(): UpdateMediaRequest
    {
        return $this->updateMediaRequest;
    }
}
