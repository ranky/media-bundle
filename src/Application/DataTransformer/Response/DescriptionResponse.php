<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Application\DataTransformer\Response;


use Ranky\MediaBundle\Domain\ValueObject\Description;
use Ranky\SharedBundle\Application\Dto\ResponseDtoInterface;


final class DescriptionResponse implements ResponseDtoInterface
{

    public function __construct(private readonly string $alt, private readonly string $title)
    {
    }

    public static function fromDescription(Description $description): self
    {
        return new self(
            $description->alt(),
            $description->title()
        );
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
     * @return array{alt: string, title: string}
     */
    public function toArray(): array
    {
        return [
            'alt' => $this->alt(),
            'title' => $this->title(),
        ];
    }

    /**
     * @return array{alt: string, title: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }


}
