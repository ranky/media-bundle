<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Local;

use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Exception\InvalidBreakpointException;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalTemporaryFilePathResolver;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;

class LocalTemporaryFilePathResolverTest extends BaseUnitTestCase
{
    private string $temporaryDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->temporaryDirectory = \sys_get_temp_dir().'/ranky_media_bundle';
    }

    public function testItShouldResolveLocalPath(): void
    {
        $localFilePathResolver = new LocalTemporaryFilePathResolver($this->temporaryDirectory);

        // absolute path
        $this->assertSame(
            $this->temporaryDirectory,
            $localFilePathResolver->resolve('/')
        );
        $this->assertSame(
            $this->temporaryDirectory.'/test',
            $localFilePathResolver->resolve('test')
        );
        $this->assertSame(
            $this->temporaryDirectory.'/test',
            $localFilePathResolver->resolve('/test/')
        );

        $this->assertSame(
            $this->temporaryDirectory.'/test/image.jpg',
            $localFilePathResolver->resolve('test/image.jpg')
        );
        $this->assertSame(
            $this->temporaryDirectory.'/test/image.jpg',
            $localFilePathResolver->resolve('/test/image.jpg')
        );
    }

    public function testItShouldResolveLocalPathFromBreakpoint(): void
    {
        $localFilePathResolver = new LocalTemporaryFilePathResolver($this->temporaryDirectory);

        $this->assertSame(
            $this->temporaryDirectory.'/'.Breakpoint::LARGE->value.'/test',
            $localFilePathResolver->resolve('test', Breakpoint::LARGE->value)
        );
        $this->assertSame(
            $this->temporaryDirectory.'/'.Breakpoint::LARGE->value.'/test',
            $localFilePathResolver->resolve('/test/', Breakpoint::LARGE->value)
        );

        $this->assertSame(
            $this->temporaryDirectory.'/'.Breakpoint::LARGE->value.'/test/image.jpg',
            $localFilePathResolver->resolve('test/image.jpg', Breakpoint::LARGE->value)
        );

        $this->assertSame(
            $this->temporaryDirectory.'/'.Breakpoint::LARGE->value.'/test/image.jpg',
            $localFilePathResolver->resolve('/test/image.jpg', Breakpoint::LARGE->value)
        );


        $this->expectException(InvalidBreakpointException::class);
        $localFilePathResolver->resolve('/test/', 'random_breakpoint');
    }
}
