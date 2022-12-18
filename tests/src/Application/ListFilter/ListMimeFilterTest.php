<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Application\ListFilter;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\ListFilter\ListMimeFilter\ListMimeFilter;
use Ranky\MediaBundle\Domain\Contract\MimeMediaRepositoryInterface;

class ListMimeFilterTest extends TestCase
{

    public function testItShouldListMimeFilter(): void
    {
        $mimeMediaRepository = $this->createMock(MimeMediaRepositoryInterface::class);
        $mimeMediaRepository
            ->expects($this->once())
            ->method('getAllByType')
            ->willReturn([
                [
                    'mimeType' => 'image',
                    'count'    => 5,
                ],
            ]);

        $listMimeFilter         = new ListMimeFilter($mimeMediaRepository);
        $listMimeFilterResponse = $listMimeFilter->__invoke();

        $this->assertSame([
            'label' => 'image (5)',
            'value' => 'image',
        ], $listMimeFilterResponse[0]->toArray());
    }

}
