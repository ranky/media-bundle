<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\DataTransformer\Response;


use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\SharedBundle\Application\Dto\ResponseDtoInterface;

final class DimensionResponse implements ResponseDtoInterface
{

    public function __construct(private readonly ?int $width = null, private readonly ?int $height = null)
    {
    }

    public static function fromDimension(Dimension $dimension): self
    {
        return new self(
            $dimension->width(),
            $dimension->height()
        );
    }

    public function width(): ?int
    {
        return $this->width;
    }

    public function height(): ?int
    {
        return $this->height;
    }

    public function asString(): string
    {
        if ($this->width && $this->height) {
            return $this->width.' x '.$this->height;
        }

        return '';
    }

    public function __toString(): string
    {
        return $this->asString();
    }

    /**
     * @return array<string,string|int|null>
     */
    public function toArray(): array
    {
        return [
            'width' => $this->width(),
            'height' => $this->height(),
            'label' => $this->asString(),
        ];
    }

    /**
     * @return array<string,string|int|null>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
