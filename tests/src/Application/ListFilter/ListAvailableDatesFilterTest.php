<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\ListFilter;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\ListFilter\ListAvailableDatesFilter\ListAvailableDatesFilter;
use Ranky\MediaBundle\Domain\Contract\AvailableDatesMediaRepository;

class ListAvailableDatesFilterTest extends TestCase
{

    public function testItShouldListAvailableDatesFilter(): void
    {
        $availableDatesMediaRepository = $this->createMock(AvailableDatesMediaRepository::class);
        $availableDatesMediaRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([[
                'year'  => 2022,
                'month' => 10,
                'count' => 5,
            ]]);

        $listAvailableDatesFilter         = new ListAvailableDatesFilter($availableDatesMediaRepository);
        $listAvailableDatesFilterResponse = $listAvailableDatesFilter->__invoke();

        $this->assertSame([
            'label' => 'October 2022 (5)',
            'value' => '2022-10',
        ], $listAvailableDatesFilterResponse[0]->toArray());
    }

}
