<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Presentation\Twig;


use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Presentation\Twig\MediaTwigExtension;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;

class MediaTwigExtensionTest extends BaseIntegrationTestCase
{
    private MediaTwigExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = $this->getService(MediaTwigExtension::class);
    }

    public function testItShouldGetHumanFileSize(): void
    {
        $twigFilter = $this->extension->getFilters()[0]->getCallable();
        $this->assertSame('200 B', \is_callable($twigFilter) ? $twigFilter(200) : '');
    }

    public function testItShouldGetMediaUrl(): void
    {
        $this->assertSame(
            $this->getUploadUrl().'/image.jpg',
            $this->extension->mediaUrl('image.jpg')
        );
    }

    public function testItShouldGetMediaThumbnailUrl(): void
    {
        $this->assertSame(
            $this->getUploadUrl().'/'.Breakpoint::LARGE->value.'/image.jpg',
            $this->extension->mediaUrl('image.jpg', Breakpoint::LARGE->value)
        );
    }
}
