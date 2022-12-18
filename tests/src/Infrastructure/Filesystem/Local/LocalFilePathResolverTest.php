<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Local;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalFilePathResolver;

class LocalFilePathResolverTest extends TestCase
{
    private string $uploadDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadDirectory = sys_get_temp_dir().'/ranky_media_bundle_test/upload';
    }

    public function testItShouldResolveLocalPath(): void
    {
        $localFilePathResolver = new LocalFilePathResolver($this->uploadDirectory);

        $this->assertSame($this->uploadDirectory, $localFilePathResolver->resolve());
        $this->assertSame($this->uploadDirectory.'/test', $localFilePathResolver->resolve('test'));
        $this->assertSame($this->uploadDirectory.'/test', $localFilePathResolver->resolve('/test/'));
    }

    public function testItShouldResolveLocalPathFromBreakpoint(): void
    {
        $localFilePathResolver = new LocalFilePathResolver($this->uploadDirectory);

        $this->assertSame(
            $this->uploadDirectory.'/'.Breakpoint::LARGE->value,
            $localFilePathResolver->resolveFromBreakpoint(Breakpoint::LARGE->value)
        );
        $this->assertSame(
            $this->uploadDirectory.'/'.Breakpoint::LARGE->value.'/test',
            $localFilePathResolver->resolveFromBreakpoint(Breakpoint::LARGE->value, 'test')
        );
        $this->assertSame(
            $this->uploadDirectory.'/'.Breakpoint::LARGE->value.'/test',
            $localFilePathResolver->resolveFromBreakpoint(Breakpoint::LARGE->value, '/test/')
        );

        $this->expectException(\InvalidArgumentException::class);
        $localFilePathResolver->resolveFromBreakpoint('random_breakpoint', '/test/');
    }
}
