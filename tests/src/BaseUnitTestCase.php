<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Domain\Contract\UserMediaRepositoryInterface;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

abstract class BaseUnitTestCase extends TestCase
{

    public function getMediaTransformer(
        ?UserIdentifier $userIdentifier = null,
        ?string $upload = null
    ): MediaToResponseTransformer {
        $userIdentifier ??= UserIdentifier::fromString('jcarlos');
        $upload         ??= '/upload';

        $userMediaRepository = $this->createMock(UserMediaRepositoryInterface::class);
        $userMediaRepository
            ->expects($this->atLeastOnce())
            ->method('getUsernameByUserIdentifier')
            ->with($userIdentifier)
            ->willReturn($userIdentifier->value());


        return new MediaToResponseTransformer($userMediaRepository, $upload);
    }

}
