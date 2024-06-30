<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;


use Ranky\MediaBundle\Domain\Model\MediaInterface;

interface AvailableDatesMediaRepositoryInterface
{

    /**
     * @return array<MediaInterface>
     */
    public function getAll(): array;
}
