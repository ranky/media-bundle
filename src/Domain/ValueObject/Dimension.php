<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Embeddable]
final class Dimension implements \JsonSerializable
{

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private readonly ?int $width;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private readonly ?int $height;

    public function __construct(?int $width = null, ?int $height = null)
    {
        $this->width  = $width;
        $this->height = $height;
    }

    public function width(): ?int
    {
        return $this->width;
    }

    public function height(): ?int
    {
        return $this->height;
    }

    /**
     * @param array<int,mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self($data[0], $data[1] ?? null);
    }

    /**
     * @return array<string,int|null>
     */
    public function toArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

    /**
     * @return array<string,int|null>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

}
