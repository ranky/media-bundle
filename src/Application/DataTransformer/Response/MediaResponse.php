<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\DataTransformer\Response;


use Ranky\MediaBundle\Domain\Model\MediaInterface;
use Ranky\SharedBundle\Application\Dto\ResponseDtoInterface;

final class MediaResponse implements ResponseDtoInterface
{
    public const DATETIME_FORMAT = 'Y-m-d H:i';

    private string $id;

    private string $createdBy;

    private string $updatedBy;

    private \DateTimeImmutable $createdAt;

    private \DateTimeImmutable $updatedAt;

    private FileResponse $file;

    private DimensionResponse $dimension;

    private DescriptionResponse $description;

    private ThumbnailsResponse $thumbnails;

    private string $dateTimeFormat = self::DATETIME_FORMAT;

    private function __construct(MediaInterface $media, string $uploadUrl, string $createdBy, string $updatedBy)
    {
        $this->id          = (string)$media->id();
        $this->file        = FileResponse::fromFile($media->file(), $uploadUrl);
        $this->dimension   = DimensionResponse::fromDimension($media->dimension());
        $this->description = DescriptionResponse::fromDescription($media->description());
        $this->thumbnails  = ThumbnailsResponse::fromThumbnails($media->thumbnails(), $uploadUrl);
        $this->createdBy   = \ucfirst($createdBy);
        $this->updatedBy   = \ucfirst($updatedBy);
        $this->createdAt   = $media->createdAt();
        $this->updatedAt   = $media->updatedAt();
    }

    public static function fromMedia(
        MediaInterface $media,
        string $uploadUrl,
        string $createdBy,
        string $updatedBy
    ): self {
        return new self($media, $uploadUrl, $createdBy, $updatedBy);
    }

    public function withDateTimeFormat(string $format): void
    {
        $this->dateTimeFormat = $format;
    }

    public function id(): string
    {
        return $this->id;
    }


    public function file(): FileResponse
    {
        return $this->file;
    }

    public function dimension(): DimensionResponse
    {
        return $this->dimension;
    }

    public function description(): DescriptionResponse
    {
        return $this->description;
    }

    public function thumbnails(): ThumbnailsResponse
    {
        return $this->thumbnails;
    }


    public function createdBy(): string
    {
        return $this->createdBy;
    }


    public function updatedBy(): string
    {
        return $this->updatedBy;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function createdAtFormat(): string
    {
        return $this->createdAt->format($this->dateTimeFormat);
    }

    public function updatedAtFormat(): string
    {
        return $this->updatedAt->format($this->dateTimeFormat);
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'id'          => $this->id(),
            'createdAt'   => $this->createdAtFormat(),
            'updatedAt'   => $this->updatedAtFormat(),
            'createdBy'   => $this->createdBy(),
            'updatedBy'   => $this->updatedBy(),
            'file'        => $this->file(),
            'dimension'   => $this->dimension(),
            'description' => $this->description(),
            'thumbnails'  => $this->thumbnails(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
