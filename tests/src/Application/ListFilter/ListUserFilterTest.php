<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\ListFilter;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\ListFilter\ListUserFilter\ListUserFilter;
use Ranky\MediaBundle\Domain\Contract\UserMediaRepositoryInterface;

class ListUserFilterTest extends TestCase
{

    public function testItShouldListMimeFilter(): void
    {
        $userMediaRepository = $this->createMock(UserMediaRepositoryInterface::class);
        $userMediaRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                [
                    'identifier' => 'jcarlos',
                    'username'   => 'jcarlos',
                    'count'      => 5,
                ],
            ]);

        $listUserFilter         = new ListUserFilter($userMediaRepository);
        $listUserFilterResponse = $listUserFilter->__invoke();

        $this->assertSame([
            'label' => 'jcarlos (5)',
            'value' => 'jcarlos',
        ], $listUserFilterResponse[0]->toArray());
    }

}
