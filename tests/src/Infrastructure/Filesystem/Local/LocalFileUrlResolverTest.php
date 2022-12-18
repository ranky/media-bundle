<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Local;

use Ranky\MediaBundle\Domain\Contract\FileUrlResolverInterface;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalFileUrlResolver;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;

class LocalFileUrlResolverTest extends BaseIntegrationTestCase
{
    private FileUrlResolverInterface $localFileUrlResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localFileUrlResolver = $this->getService(LocalFileUrlResolver::class);
    }

    public function testItShouldResolveLocalUploadUrl(): void
    {
        $this->assertSame('/uploads/', $this->localFileUrlResolver->resolve('/'));
        $this->assertSame($_ENV['SITE_URL'].'/uploads/', $this->localFileUrlResolver->resolve('/', true));
        $this->assertSame(
            $_ENV['SITE_URL'].'/uploads/image.jpg',
            $this->localFileUrlResolver->resolve('/image.jpg', true)
        );
        $this->assertSame(
            $_ENV['SITE_URL'].'/uploads/image.jpg',
            $this->localFileUrlResolver->resolve('image.jpg', true)
        );
    }
}
