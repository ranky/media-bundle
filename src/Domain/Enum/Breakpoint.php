<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Enum;


enum Breakpoint: string
{

    case LARGE = 'large';
    case MEDIUM = 'medium';
    case SMALL = 'small';
    case XSMALL = 'xsmall';

    /**
     * @return array{int,int} | array{int}
     */
    public function dimensions(): array
    {
        return match ($this) {
            self::LARGE => [1024],
            self::MEDIUM => [768],
            self::SMALL => [576],
            self::XSMALL => [130, 130],
        };
    }

    /**
     * @return string[]
     */
    public static function breakpoints(): array
    {
        return \array_map(static fn(self $breakpoint) => $breakpoint->value, self::cases());
    }

}
