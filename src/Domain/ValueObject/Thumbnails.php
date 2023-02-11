<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\ValueObject;

use Ranky\SharedBundle\Domain\ValueObject\Collection;

/**
 * @extends Collection<Thumbnail>
 * @phpstan-import-type ThumbnailArray from \Ranky\MediaBundle\Domain\ValueObject\Thumbnail
 */
final class Thumbnails extends Collection
{

    protected function type(): string
    {
        return Thumbnail::class;
    }


    /**
     * @return array<ThumbnailArray>
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->getIterator() as $key => $item) {
            /* @var $item Thumbnail */
            /* @var $key int */
            $array[$key] = $item->toArray();
        }
        return $array;
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $items = [];
        foreach ($data as $item) {
            $items[] = new Thumbnail(
                $item['breakpoint'],
                $item['name'],
                $item['path'],
                (int)$item['size'],
                new Dimension($item['width'] ?? null, $item['height'] ?? null)
            );
        }

        return new self($items);
    }

}
