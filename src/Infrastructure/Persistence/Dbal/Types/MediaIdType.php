<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Dbal\Types;

use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\UlidType;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;

class MediaIdType extends UlidType
{
    protected function getClass(): string
    {
        return MediaId::class;
    }
}
