<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\ListFilter\ListMimeFilter;

use Ranky\SharedBundle\Application\Dto\ResponseFormFilterDtoInterface;
use Ranky\SharedBundle\Domain\ValueObject\MappingTrait;

class ListMimeFilterResponse implements ResponseFormFilterDtoInterface
{

    use MappingTrait;

    private function __construct(private readonly string $mimeType, private readonly int $count)
    {
    }


    public function value(): string
    {
        return $this->mimeType;
    }

    public function label(): string
    {
        return \sprintf('%s (%d)', $this->mimeType, $this->count);
    }

    /**
     * @param array<string,mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            self::getString($data, 'mimeType'),
            self::getInt($data, 'count')
        );
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label(),
            'value' => $this->value(),
        ];
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
       return $this->toArray();
    }
}
