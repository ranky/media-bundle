<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\ListFilter;


use Ranky\MediaBundle\Application\ListFilter\ListAvailableDatesFilter\ListAvailableDatesFilterResponse;
use Ranky\MediaBundle\Application\ListFilter\ListMimeFilter\ListMimeFilter;
use Ranky\MediaBundle\Application\ListFilter\ListAvailableDatesFilter\ListAvailableDatesFilter;
use Ranky\MediaBundle\Application\ListFilter\ListMimeFilter\ListMimeFilterResponse;
use Ranky\MediaBundle\Application\ListFilter\ListUserFilter\ListUserFilter;
use Ranky\MediaBundle\Application\ListFilter\ListUserFilter\ListUserFilterResponse;

class ListFilter
{
    public function __construct(
        private readonly ListAvailableDatesFilter $listAvailableDatesFilter,
        private readonly ListMimeFilter $listMimeFilter,
        private readonly ListUserFilter $listUserFilter
    ) {
    }

    /**
     * @return array{availableDates: array<ListAvailableDatesFilterResponse>, mimeTypes: array<ListMimeFilterResponse>, users: array<ListUserFilterResponse> }
     */
    public function __invoke(): array
    {
        return [
            'availableDates' => $this->listAvailableDatesFilter->__invoke(),
            'mimeTypes' => $this->listMimeFilter->__invoke(),
            'users' => $this->listUserFilter->__invoke(),
        ];
    }
}
