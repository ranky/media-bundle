<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Dummy\Page\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ranky\MediaBundle\Domain\Model\MediaInterface;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;

#[ORM\Entity]
#[ORM\Table(name: 'page')]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;


    #[ORM\Column(name: 'media_id', type: 'media_id', nullable: true)]
    private ?MediaId $mediaId;

    /**
     * @var array<int,string>|null
     */
    #[ORM\Column(name: 'gallery', type: Types::JSON, nullable: true)]
    private ?array $gallery;


    #[ORM\ManyToOne(targetEntity: MediaInterface::class)]
    #[ORM\JoinColumn(name: 'media', referencedColumnName: 'id', nullable: true)]
    private ?MediaInterface $media;

    /**
     * Many pages have Many Media Files.
     * @var ?Collection<int, MediaInterface>
     */
    #[ORM\JoinTable(name: 'pages_medias')]
    #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'media_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: MediaInterface::class)]
    //#[ORM\OrderBy(['createdAt' => 'DESC'])]
    private ?Collection $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMediaId(): ?MediaId
    {
        return $this->mediaId;
    }

    public function setMediaId(?MediaId $mediaId): self
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    /**
     * @return array<int,string>|null
     */
    public function getGallery(): ?array
    {
        return $this->gallery;
    }

    /**
     * @param array<int,string>|null $gallery
     * @return $this
     */
    public function setGallery(?array $gallery): self
    {
        $this->gallery = $gallery;

        return $this;
    }

    public function getMedia(): ?MediaInterface
    {
        return $this->media;
    }

    public function setMedia(?MediaInterface $media): self
    {
        $this->media = $media;

        return $this;
    }

    /** @return ?Collection<int, MediaInterface> */
    public function getMedias(): ?Collection
    {
        return $this->medias;
    }

    /**
     * @param ?Collection<int, MediaInterface> $medias
     * @return self
     */
    public function setMedias(?Collection $medias): self
    {
        $this->medias = $medias;

        return $this;
    }


    public function addMedia(MediaInterface $media): self
    {
        if ($this->medias && !$this->medias->contains($media)) {
            $this->medias[] = $media;
        }

        return $this;
    }

    public function removeMedia(MediaInterface $media): self
    {
        if ($this->medias && $this->medias->contains($media)) {
            $this->medias->removeElement($media);
        }

        return $this;
    }
}
