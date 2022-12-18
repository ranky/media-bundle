<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\CreateMedia;

use Ranky\SharedBundle\Application\Dto\RequestDtoInterface;
use Ranky\SharedBundle\Domain\ValueObject\MappingTrait;

/**
 * @phpstan-type UploadedFileRequestArray array{path: string, name: string, mime: string, extension: string, size: int}
 */
final class UploadedFileRequest implements RequestDtoInterface
{
    use MappingTrait;

    public function __construct(
        private readonly string $path,
        private readonly string $name,
        private readonly string $mime,
        private readonly string $extension,
        private readonly int $size
    ) {
    }


    public function name(): string
    {
        return $this->name;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function mime(): string
    {
        return $this->mime;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    public function size(): int
    {
        return $this->size;
    }

    /**
     * @param UploadedFileRequestArray|array<string, mixed> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            self::getString($data, 'path'),
            self::getString($data, 'name'),
            self::getString($data, 'mime'),
            self::getString($data, 'extension'),
            self::getInt($data, 'size')
        );
    }

    /**
     * @return UploadedFileRequestArray
     */
    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'name' => $this->name,
            'mime' => $this->mime,
            'extension' => $this->extension,
            'size' => $this->size,
        ];
    }

    /**
     * @return UploadedFileRequestArray
     */
    public function jsonSerialize(): array
    {
       return $this->toArray();
    }
}
