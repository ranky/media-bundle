<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\ListMediaCriteria;

use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\ListMediaCriteria\ListMediaCriteria;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Criteria\MediaCriteria;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;

class ListMediaCriteriaTest extends BaseUnitTestCase
{
    /**
     * @throws \Exception
     */
    public function testItShouldListMedia(): void
    {
        $media            = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $userIdentifier   = $media->createdBy();
        $uploadUrl        = '/uploads';
        $offsetPagination = new OffsetPagination(1, 10);
        $orderBy          = new OrderBy('id', OrderBy::DESC);
        $criteria         = new MediaCriteria([], $offsetPagination, $orderBy);

        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $mediaRepository
            ->expects($this->once())
            ->method('filter')
            ->with($criteria)
            ->willReturn([$media]);
        $mediaRepository
            ->expects($this->once())
            ->method('size')
            ->with($criteria)
            ->willReturn(1);


        $responseTransformer = $this->getMediaTransformer($userIdentifier, $uploadUrl);

        $listMediaPagination = (new ListMediaCriteria(
            $mediaRepository,
            $responseTransformer
        ))->__invoke($criteria);


        $this->assertSame(1, $listMediaPagination->count());
        $this->assertContainsEquals(
            MediaResponse::fromMedia(
                $media,
                $uploadUrl,
                $media->createdBy()->value(),
                $media->updatedBy()->value()
            ),
            $listMediaPagination
        );
    }

}
