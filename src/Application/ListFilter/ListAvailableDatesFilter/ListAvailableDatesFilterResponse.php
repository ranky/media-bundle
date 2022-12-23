<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\ListFilter\ListAvailableDatesFilter;

use Ranky\SharedBundle\Application\Dto\ResponseFormFilterDtoInterface;
use Ranky\SharedBundle\Domain\ValueObject\MappingTrait;

class ListAvailableDatesFilterResponse implements ResponseFormFilterDtoInterface
{
    use MappingTrait;

    private function __construct(private readonly int $year, private readonly int $month, private readonly int $count)
    {
    }


    public function value(): string
    {
        return \sprintf('%d-%d', $this->year, $this->month);
    }

    public function label(): string
    {
        /** @phpstan-ignore-next-line */
        $dateLabel = (string)\IntlDateFormatter::create(locale: null, pattern: 'MMMM Y')
            ->format(
                \DateTimeImmutable::createFromFormat(
                    '!Ym',
                    \sprintf('%d%d', $this->year, $this->month)
                )
            );


        return \sprintf('%s (%d)', \ucfirst($dateLabel), $this->count);
    }

    public function toReverseDate(): \DateTimeImmutable|string
    {
        return \DateTimeImmutable::createFromFormat(
            '!Y-m',
            \sprintf('%d-%d', $this->year, $this->month)
        ) ?: '';
    }

    /**
     * @param array<string,mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            self::getInt($data, 'year'),
            self::getInt($data, 'month'),
            self::getInt($data, 'count'),
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
