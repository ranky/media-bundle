<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\ListFilter;


use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\ListFilter\ListAvailableDatesFilter\ListAvailableDatesFilter;
use Ranky\MediaBundle\Application\ListFilter\ListFilter;
use Ranky\MediaBundle\Application\ListFilter\ListMimeFilter\ListMimeFilter;
use Ranky\MediaBundle\Application\ListFilter\ListUserFilter\ListUserFilter;

class ListFilterTest extends TestCase
{

    public function testItShouldListFilter(): void
    {
        $listAvailableDatesData   = [
            'label' => 'Octubre 2022 (5)',
            'value' => '2022-10',
        ];
        $listAvailableDatesFilter = $this->createMock(ListAvailableDatesFilter::class);
        $listAvailableDatesFilter
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn($listAvailableDatesData);

        $listMimeData   = [
            'label' => 'Image (5)',
            'value' => 'image',
        ];
        $listMimeFilter = $this->createMock(ListMimeFilter::class);
        $listMimeFilter
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn($listMimeData);

        $listUserData   = [
            'label' => 'jcarlos (5)',
            'value' => 'jcarlos',
        ];
        $listUserFilter = $this->createMock(ListUserFilter::class);
        $listUserFilter
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn($listUserData);

        $listFilter = new ListFilter(
            $listAvailableDatesFilter,
            $listMimeFilter,
            $listUserFilter
        );

        $listFilter = $listFilter->__invoke();

        $this->assertSame($listAvailableDatesData, $listFilter['availableDates']);
        $this->assertSame($listMimeData, $listFilter['mimeTypes']);
        $this->assertSame($listUserData, $listFilter['users']);
    }

}
