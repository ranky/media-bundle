<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;


use Ranky\MediaBundle\Domain\Model\Media;

interface AvailableDatesMediaRepositoryInterface
{

    /**
     * @return array<Media>
     */
    public function getAll(): array;
}
