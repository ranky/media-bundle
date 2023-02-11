<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @phpstan-type DimensionArray array{width: int|null, height: int|null}
 */
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

    public function __toString(): string
    {
        return (string)($this->width ?? ''). 'x' .(string)($this->height ?? '');
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
     * @param array<int> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self($data[0] ?? null, $data[1] ?? null);
    }

    /**
     * @return DimensionArray
     */
    public function toArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

    /**
     * @return DimensionArray
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

}
