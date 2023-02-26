<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Flysystem;

use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem\FlysystemFileUrlResolver;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;

class FlysystemFileUrlResolverTest extends BaseIntegrationTestCase
{

    public function testItShouldResolveLocalRelativeUrl(): void
    {
        $uploadUrl = '/uploads';
        $flysystemFileUrlResolver = new FlysystemFileUrlResolver(
            $uploadUrl
        );
        $this->assertSame(
            $uploadUrl,
            $flysystemFileUrlResolver->resolve('/')
        );
        $this->assertSame(
            $uploadUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve('/image.jpg')
        );
        $this->assertSame(
            $uploadUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve('image.jpg')
        );
        // with upload url in path
        $this->assertSame(
            $uploadUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve('/uploads/image.jpg')
        );

        // breakpoint
        $smallBreakpoint = Breakpoint::SMALL->value;
        $breakpointUrl = $uploadUrl.'/'.$smallBreakpoint;
        $this->assertSame(
            $breakpointUrl,
            $flysystemFileUrlResolver->resolve('/', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve('/image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve('image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl.'/small.image.jpg',
            $flysystemFileUrlResolver->resolve('/small.image.jpg', $smallBreakpoint)
        );
        // with upload url in path
        $this->assertSame(
            $breakpointUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve('/uploads/image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve('/uploads/small/image.jpg', $smallBreakpoint)
        );
    }

    public function testItShouldResolveLocalAbsoluteUrl(): void
    {
        $uploadUrl = $_ENV['SITE_URL'].'/uploads';
        $flysystemFileUrlResolver = new FlysystemFileUrlResolver(
            $uploadUrl
        );
        $this->assertSame(
            $uploadUrl,
            $flysystemFileUrlResolver->resolve('/')
        );
        $this->assertSame(
            $uploadUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve('/image.jpg')
        );
        $this->assertSame(
            $uploadUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve('image.jpg')
        );

        // breakpoint
        $smallBreakpoint = Breakpoint::SMALL->value;
        $breakpointUrl = \sprintf('%s/%s/%s', $uploadUrl, $smallBreakpoint, 'image.jpg');
        $this->assertSame(
            $breakpointUrl,
            $flysystemFileUrlResolver->resolve('image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl,
            $flysystemFileUrlResolver->resolve('/image.jpg', $smallBreakpoint)
        );

        // with upload url in path
        $this->assertSame(
            $uploadUrl.'/image.jpg',
            $flysystemFileUrlResolver->resolve($uploadUrl.'/image.jpg')
        );
        $this->assertSame(
            $breakpointUrl,
            $flysystemFileUrlResolver->resolve($uploadUrl.'/small/image.jpg', $smallBreakpoint)
        );

    }

    /**
     * @throws \Exception
     */
    public function testItShouldResolveRemoteAbsoluteUrlWithSubdomain(): void
    {
        $uploadUrl           = 'https://subdomain.rankymedia.com/uploads/documents';

        $fileUrlResolver = new FlysystemFileUrlResolver(
            $uploadUrl
        );

        $this->assertSame(
            $uploadUrl,
            $fileUrlResolver->resolve('/')
        );
        $this->assertSame(
            $uploadUrl.'/image.jpg',
            $fileUrlResolver->resolve('/image.jpg')
        );
        $this->assertSame(
            $uploadUrl.'/image.jpg',
            $fileUrlResolver->resolve('image.jpg')
        );

        // breakpoint
        $smallBreakpoint = Breakpoint::SMALL->value;
        $breakpointUrl = \sprintf('%s/%s/%s', $uploadUrl, $smallBreakpoint, 'image.jpg');
        $this->assertSame(
            $breakpointUrl,
            $fileUrlResolver->resolve('image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl,
            $fileUrlResolver->resolve('/image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl,
            $fileUrlResolver->resolve($uploadUrl.'/image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $breakpointUrl,
            $fileUrlResolver->resolve('/'.$smallBreakpoint.'/image.jpg', $smallBreakpoint)
        );
    }
}
