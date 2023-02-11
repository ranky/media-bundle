<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Domain\Contract\FileUrlResolver;
use Ranky\MediaBundle\Domain\Contract\TemporaryFileRepository;
use Ranky\MediaBundle\Domain\Contract\UserMediaRepository;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;


abstract class BaseUnitTestCase extends TestCase
{

    public function getMediaTransformer(
        ?UserIdentifier $userIdentifier = null,
    ): MediaToResponseTransformer {
        $userIdentifier ??= UserIdentifier::fromString('jcarlos');

        $userMediaRepository = $this->createMock(UserMediaRepository::class);
        $userMediaRepository
            ->expects($this->atLeastOnce())
            ->method('getUsernameByUserIdentifier')
            ->with($userIdentifier)
            ->willReturn($userIdentifier->value());


        return new MediaToResponseTransformer(
            $userMediaRepository,
            $this->getFileUrlResolver()
        );
    }


    public function getFileUrlResolver(): FileUrlResolver
    {
        $fileUrlResolver = $this->createMock(FileUrlResolver::class);
        $fileUrlResolver
            ->expects($this->atLeastOnce())
            ->method('resolve')
            ->willReturnCallback(fn (string $path) => $_ENV['SITE_URL'].'/uploads/'.$path);

        return $fileUrlResolver;
    }

    public function getTemporaryFileRepository(string $path): TemporaryFileRepository&MockObject
    {
        $temporaryFileRepository = $this->createMock(TemporaryFileRepository::class);

        $temporaryFileRepository
            ->expects($this->once())
            ->method('temporaryFile')
            ->with($path)
            ->willReturn($this->getTemporaryDirectory($path));

        return $temporaryFileRepository;
    }

    public function getTmpUploadDirectory(?string $path = null): string
    {
        $fullPath = \sys_get_temp_dir().'/ranky_media_bundle_test/uploads';
        if ($path) {
            $fullPath .= '/'.\ltrim($path, '/');
        }
        return $fullPath;
    }

    public function getUploadUrl(): string
    {
        return $_ENV['SITE_URL'].'/uploads';
    }

    public function getTemporaryDirectory(string $path): string
    {
        return \sys_get_temp_dir().'/ranky_media_bundle/'.\ltrim($path, '/');
    }

    /**
     * @return array<string, array<int, int>|array<int>>
     */
    public function getBreakpoints(): array
    {
        return array_reduce(Breakpoint::cases(), static function ($breakpoints, $breakpoint) {
            $breakpoints[$breakpoint->value] = $breakpoint->dimensions();
            return $breakpoints;
        }, []);
    }

}
