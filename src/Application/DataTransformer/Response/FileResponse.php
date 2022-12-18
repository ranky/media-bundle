<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\DataTransformer\Response;


use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\SharedBundle\Application\Dto\ResponseDtoInterface;
use Ranky\SharedBundle\Common\FileHelper;

final class FileResponse implements ResponseDtoInterface
{

    private readonly string $baseName;

    public function __construct(
        private readonly string $name,
        private readonly string $url,
        private readonly string $mime,
        private readonly string $extension,
        private readonly int $size
    ) {
        $this->baseName = \pathinfo($name, \PATHINFO_FILENAME);
    }

    public static function fromFile(File $file, string $uploadUrl): self
    {
        $url = sprintf('%s/%s', $uploadUrl, ltrim($file->path(), '/'));

        return new self(
            $file->name(),
            $url,
            $file->mime(),
            $file->extension(),
            $file->size()
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function mime(): string
    {
        return $this->mime;
    }

    public function mimeType(): string
    {
        return explode('/', $this->mime)[0];
    }

    public function mimeSubType(): string
    {
        return explode('/', $this->mime)[1];
    }

    public function extension(): string
    {
        return $this->extension;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function humanSize(): string
    {
        return FileHelper::humanFileSize($this->size);
    }

    public function baseName(): string
    {
        return $this->baseName;
    }

    /**
     * @return array<string,string|int>
     */
    public function toArray(): array
    {
        return [
            'name'        => $this->name(),
            'url'         => $this->url(),
            'basename'    => $this->baseName(),
            'mime'        => $this->mime(),
            'mimeType'    => $this->mimeType(),
            'mimeSubType' => $this->mimeSubType(),
            'extension'   => $this->extension(),
            'size'        => $this->size(),
            'humanSize'   => $this->humanSize(),
        ];
    }

    /**
     * @return array<string,string|int>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

}
