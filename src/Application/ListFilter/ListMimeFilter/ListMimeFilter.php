<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\ListFilter\ListMimeFilter;

use Ranky\MediaBundle\Domain\Contract\MimeMediaRepository;

class ListMimeFilter
{

    public function __construct(private readonly MimeMediaRepository $mimeMediaRepository)
    {
    }

    /**
     * @return array<ListMimeFilterResponse>
     */
    public function __invoke(): array
    {
        return \array_map(
            static fn($mediaData) => ListMimeFilterResponse::fromArray((array)$mediaData),
            $this->mimeMediaRepository->getAllByType(),
            []
        );
    }
}
