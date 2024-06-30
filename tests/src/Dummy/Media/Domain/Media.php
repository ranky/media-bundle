<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Dummy\Media\Domain;

use Doctrine\ORM\Mapping as ORM;
use Ranky\MediaBundle\Domain\Model\Media as BaseMedia;
use Ranky\MediaBundle\Tests\Dummy\Media\Infrastructure\MediaRepository;

#[ORM\Table(name: 'ranky_media')]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media extends BaseMedia
{

}
