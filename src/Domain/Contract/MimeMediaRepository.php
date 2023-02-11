<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;


use Ranky\MediaBundle\Domain\Model\Media;

interface MimeMediaRepository
{

    /**
     * @return array<Media>
     */
    public function getAll(): array;

    /**
     * @return array<Media>
     */
    public function getAllByType(): array;

    /**
     * @return array<Media>
     */
    public function getAllBySubType(): array;
}
