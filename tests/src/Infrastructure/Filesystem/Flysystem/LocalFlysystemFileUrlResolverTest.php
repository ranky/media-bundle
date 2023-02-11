<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Flysystem;

use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Exception\InvalidPathException;
use Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem\FlysystemFileUrlResolver;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;

class LocalFlysystemFileUrlResolverTest extends BaseIntegrationTestCase
{
    private FlysystemFileUrlResolver $flysystemFileUrlResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->flysystemFileUrlResolver = $this->getService(FlysystemFileUrlResolver::class);
    }

    public function testItShouldResolveLocalRelativeUrl(): void
    {

        $this->assertSame(
            '/uploads',
            $this->flysystemFileUrlResolver->resolve('/', null, false)
        );
        $this->assertSame(
            '/uploads/image.jpg',
            $this->flysystemFileUrlResolver->resolve('/uploads/image.jpg', null, false)
        );
        $this->assertSame(
            '/uploads/image.jpg',
            $this->flysystemFileUrlResolver->resolve('/image.jpg', null, false)
        );
        $this->assertSame(
            '/uploads/image.jpg',
            $this->flysystemFileUrlResolver->resolve('image.jpg', null, false)
        );

        // breakpoint
        $smallBreakpoint = Breakpoint::SMALL->value;
        $this->assertSame(
            '/uploads/'.$smallBreakpoint,
            $this->flysystemFileUrlResolver->resolve('/', $smallBreakpoint, false)
        );
        $this->assertSame(
            '/uploads/'.$smallBreakpoint.'/image.jpg',
            $this->flysystemFileUrlResolver->resolve('/uploads/image.jpg', $smallBreakpoint, false)
        );
        $this->assertSame(
            '/uploads/'.$smallBreakpoint.'/image.jpg',
            $this->flysystemFileUrlResolver->resolve('/image.jpg', $smallBreakpoint, false)
        );
        $this->assertSame(
            '/uploads/'.$smallBreakpoint.'/image.jpg',
            $this->flysystemFileUrlResolver->resolve('image.jpg', $smallBreakpoint, false)
        );

        $this->expectExceptionObject(InvalidPathException::withUrl($_ENV['SITE_URL'].'/'));
        $this->flysystemFileUrlResolver->resolve($_ENV['SITE_URL'].'/', null, false);
    }

    public function testItShouldResolveLocalAbsoluteUrl(): void
    {

        $this->assertSame(
            $_ENV['SITE_URL'].'/uploads',
            $this->flysystemFileUrlResolver->resolve('/')
        );
        $this->assertSame(
            $_ENV['SITE_URL'].'/uploads/image.jpg',
            $this->flysystemFileUrlResolver->resolve('/image.jpg')
        );
        $this->assertSame(
            $_ENV['SITE_URL'].'/uploads/image.jpg',
            $this->flysystemFileUrlResolver->resolve('image.jpg')
        );

        // breakpoint
        $smallBreakpoint = Breakpoint::SMALL->value;
        $breakpointUrl = \sprintf('%s/%s/%s', $_ENV['SITE_URL'].'/uploads', $smallBreakpoint, 'image.jpg');
        $this->assertSame(
            $breakpointUrl,
            $this->flysystemFileUrlResolver->resolve('image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl,
            $this->flysystemFileUrlResolver->resolve('/image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl,
            $this->flysystemFileUrlResolver->resolve('/uploads/image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl,
            $this->flysystemFileUrlResolver->resolve('/uploads/'.$smallBreakpoint.'/image.jpg', $smallBreakpoint)
        );


        $this->expectExceptionObject(InvalidPathException::withUrl($_ENV['SITE_URL']));
        $this->flysystemFileUrlResolver->resolve($_ENV['SITE_URL']);
    }
}
