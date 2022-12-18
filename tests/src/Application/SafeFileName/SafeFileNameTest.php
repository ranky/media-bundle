<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application\SafeFileName;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Application\SafeFileName\SafeFileName;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Exception\NotFoundMediaException;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\SharedBundle\Common\FileHelper;

class SafeFileNameTest extends TestCase
{

    /**
     * @dataProvider getResultRepository
     */
    public function testItShouldSafeFileName(string $name, string $extension, mixed $result): void
    {
        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $mediaMethod = $mediaRepository
            ->expects($this->once())
            ->method('getByFileName')
            ->with($name);

        if ($result instanceof Media){
            $mediaMethod->willReturn($result);
        }else{
            $mediaMethod->willThrowException($result);
        }


        $safeFileName = (new SafeFileName($mediaRepository))->__invoke(
            $name,
            $extension
        );

        if ($result instanceof Media) {
            $this->assertNotSame($name, $safeFileName);
        } else {
            $this->assertSame($name, $safeFileName);
        }
    }

    /**
     * @return array<int,array<int, mixed>>
     */
    public function getResultRepository(): array
    {
        $media     = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $basename  = FileHelper::basename($media->file()->name());
        $extension = $media->file()->extension();
        $name      = \sprintf('%s.%s', $basename, $extension);

        return [
            [$name, $extension, $media],
            [$name, $extension, NotFoundMediaException::withFileName($name)],
        ];
    }


}
