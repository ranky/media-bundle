<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Dbal\Types;

use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\UlidType;

class MediaIdType extends UlidType
{
    protected function getClass(): string
    {
        return MediaId::class;
    }
}
