<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\DataTransformer\Response;

use Ranky\MediaBundle\Domain\ValueObject\Thumbnail;
use Ranky\SharedBundle\Common\FileHelper;

final class ThumbnailResponse
{

    public function __construct(
        private readonly string $breakpoint,
        private readonly string $name,
        private readonly string $url,
        private readonly int $size,
        private readonly DimensionResponse $dimension = new DimensionResponse()
    ) {
    }

    public static function fromThumbnail(Thumbnail $thumbnail, string $uploadUrl): self
    {
        $url = sprintf('%s/%s', $uploadUrl, ltrim($thumbnail->path(), '/'));

        return new self(
            $thumbnail->breakpoint(),
            $thumbnail->name(),
            $url,
            $thumbnail->size(),
            DimensionResponse::fromDimension($thumbnail->dimension())
        );
    }

    public function breakpoint(): string
    {
        return $this->breakpoint;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function dimension(): DimensionResponse
    {
        return $this->dimension;
    }

    public function humanSize(): string
    {
        return FileHelper::humanFileSize($this->size);
    }


}
