<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Flysystem;

use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Exception\InvalidPathException;
use Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem\FlysystemFileUrlResolver;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\SharedBundle\Domain\Site\SiteUrlResolverInterface;

class RemoteFlysystemFileUrlResolverTest extends BaseIntegrationTestCase
{
    private FlysystemFileUrlResolver $flysystemFileUrlResolver;
    private string $remoteUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $adapter                        = 'aws';
        $this->remoteUrl                = 'https://bucket.s3.amazonaws.com';
        $this->flysystemFileUrlResolver = new FlysystemFileUrlResolver(
            $this->remoteUrl,
            $adapter,
            $this->getService(SiteUrlResolverInterface::class)
        );
    }

    public function testIShouldResolveValidRemoteMediaFiles(): void
    {
        $this->assertSame(
            $this->remoteUrl.'/',
            $this->flysystemFileUrlResolver->resolve('/')
        );

        $remoteUrlImage = \sprintf('%s/%s', $this->remoteUrl, 'image.jpg');
        $this->assertSame(
            $remoteUrlImage,
            $this->flysystemFileUrlResolver->resolve('/image.jpg')
        );
        $this->assertSame(
            $remoteUrlImage,
            $this->flysystemFileUrlResolver->resolve('image.jpg')
        );
        $this->assertNotSame(
            $remoteUrlImage,
            $this->flysystemFileUrlResolver->resolve('/uploads/image.jpg')
        );
        // breakpoint
        $smallBreakpoint = Breakpoint::SMALL->value;
        $remoteUrlBreakpoint = \sprintf('%s/%s/%s', $this->remoteUrl, $smallBreakpoint, 'image.jpg');
        $this->assertSame(
            $remoteUrlBreakpoint,
            $this->flysystemFileUrlResolver->resolve('image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $remoteUrlBreakpoint,
            $this->flysystemFileUrlResolver->resolve('/image.jpg', $smallBreakpoint)
        );

        $this->assertSame(
            $remoteUrlBreakpoint,
            $this->flysystemFileUrlResolver->resolve('/small/image.jpg', $smallBreakpoint)
        );
        $this->assertSame(
            $remoteUrlBreakpoint,
            $this->flysystemFileUrlResolver->resolve('small/image.jpg', $smallBreakpoint)
        );

        $this->expectExceptionObject(InvalidPathException::withUrl($this->remoteUrl));
        $this->flysystemFileUrlResolver->resolve($this->remoteUrl);
    }
}
