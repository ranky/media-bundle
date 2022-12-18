<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\DataTransformer\Response;


use Ranky\MediaBundle\Domain\ValueObject\Thumbnail;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnails;
use Ranky\SharedBundle\Domain\ValueObject\Collection;

/**
 * @template-extends Collection<ThumbnailResponse>
 */
final class ThumbnailsResponse extends Collection
{


    protected function type(): string
    {
        return ThumbnailResponse::class;
    }

    public static function fromThumbnails(Thumbnails $thumbnails, string $uploadUrl): self
    {
        $array = [];

        foreach ($thumbnails as $thumbnail) {
            /* @var $thumbnail Thumbnail */
            $array[] = ThumbnailResponse::fromThumbnail($thumbnail, $uploadUrl);
        }

        return new self($array);
    }


    /**
     * @throws \Exception
     * @return array<int|string,array<string,mixed>>
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->getIterator() as $key => $item) {
            /* @var $item ThumbnailResponse */
            $array[$key] = [
                'breakpoint' => $item->breakpoint(),
                'name'       => $item->name(),
                'url'        => $item->url(),
                'size'       => $item->size(),
                'humanSize'  => $item->humanSize(),
                'dimension'  => $item->dimension(),
            ];
        }

        return $array;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $items = [];
        foreach ($data as $item) {
            $items[] = new ThumbnailResponse(
                $item['breakpoint'],
                $item['name'],
                $item['path'], // TODO: convert to url from path
                (int)$item['size'],
                new DimensionResponse($item['width'] ?? null, $item['height'] ?? null)
            );
        }

        return new self($items);
    }
}
