<?php

declare(strict_types=1);


namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Ranky\MediaBundle\Domain\Model\Media as BaseMedia;

#[ORM\Table(name: 'ranky_media')]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media extends BaseMedia
{

}
