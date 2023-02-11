<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\ListFilter\ListAvailableDatesFilter;


use Ranky\MediaBundle\Domain\Contract\AvailableDatesMediaRepository;

class ListAvailableDatesFilter
{
    public function __construct(private readonly AvailableDatesMediaRepository $availableDatesMediaRepository)
    {
    }

    /**
     * @return array<ListAvailableDatesFilterResponse>
     */
    public function __invoke(): array
    {
        return \array_map(
            static fn($mediaData) => ListAvailableDatesFilterResponse::fromArray((array)$mediaData),
            $this->availableDatesMediaRepository->getAll(),
            []
        );
    }
}
