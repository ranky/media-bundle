<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\ValueObject;

/**
 * @phpstan-type ThumbnailArray array{breakpoint: string, name: string, path: string, size: int, width: int|null, height: int|null }
 */
final class Thumbnail
{

    public function __construct(
        private readonly string $breakpoint,
        private readonly string $name,
        private readonly string $path,
        private readonly int $size,
        private readonly Dimension $dimension = new Dimension()
    ) {
    }

    public function breakpoint(): string
    {
        return $this->breakpoint;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function dimension(): Dimension
    {
        return $this->dimension;
    }


    public function changeSize(int $size): self
    {
        return new self(
            $this->breakpoint,
            $this->name,
            $this->path,
            $size,
            $this->dimension
        );
    }

    public function rename(string $name, string $path): self
    {
        return new self(
            $this->breakpoint,
            $name,
            $path,
            $this->size,
            $this->dimension
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $dimension = new Dimension();
        if (isset($data['width'], $data['height'])) {
            $dimension = new Dimension($data['width'], $data['height']);
        }

        return new self(
            $data['breakpoint'],
            $data['name'],
            $data['path'],
            $data['size'],
            $data['dimension'] ?? $dimension
        );
    }

    /**
     * @return ThumbnailArray
     */
    public function toArray(): array
    {
        return [
            'breakpoint' => $this->breakpoint,
            'name'       => $this->name,
            'path'       => $this->path,
            'size'       => $this->size,
            'width'      => $this->dimension->width(),
            'height'     => $this->dimension->height(),
        ];
    }


}
