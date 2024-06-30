<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;


use Ranky\MediaBundle\Domain\Model\MediaInterface;

interface MimeMediaRepositoryInterface
{

    /**
     * @return array<MediaInterface>
     */
    public function getAll(): array;

    /**
     * @return array<MediaInterface>
     */
    public function getAllByType(): array;

    /**
     * @return array<MediaInterface>
     */
    public function getAllBySubType(): array;
}
