<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\UpdateMedia;

use Ranky\SharedBundle\Application\Dto\RequestDtoInterface;
use Ranky\SharedBundle\Domain\ValueObject\MappingTrait;

/**
 * @phpstan-type UpdateMediaRequestArray array{id: string, name: string, alt: string, title: string}
 */
class UpdateMediaRequest implements RequestDtoInterface
{
    use MappingTrait;

    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $alt,
        private readonly string $title
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function alt(): string
    {
        return $this->alt;
    }

    public function title(): string
    {
        return $this->title;
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            self::getString($data, 'id'),
            self::getString($data, 'name'),
            self::getString($data, 'alt'),
            self::getString($data, 'title'),
        );
    }

    /**
     * @return UpdateMediaRequestArray
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'alt' => $this->alt,
            'title' => $this->title,
        ];
    }

    /**
     * @return UpdateMediaRequestArray
     */
    public function jsonSerialize(): array
    {
       return $this->toArray();
    }
}
