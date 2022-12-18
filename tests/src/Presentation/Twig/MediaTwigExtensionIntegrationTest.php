<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Presentation\Twig;


use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalFileUrlResolver;
use Ranky\MediaBundle\Presentation\Twig\MediaTwigExtension;
use Ranky\SharedBundle\Domain\Site\SiteUrlResolverInterface;
use Twig\Test\IntegrationTestCase;

class MediaTwigExtensionIntegrationTest extends IntegrationTestCase
{

    protected function getExtensions(): array
    {
        $uploadUrl           = '/uploads';
        $mockSiteUrlResolver = $this->getMockBuilder(SiteUrlResolverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockSiteUrlResolver
            ->method('siteUrl')
            ->willReturn($_ENV['SITE_URL']);

        $fileUrlResolver = new LocalFileUrlResolver($mockSiteUrlResolver, $uploadUrl);

        return [
            new MediaTwigExtension($fileUrlResolver),
        ];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__.'/Fixtures/';
    }
}
