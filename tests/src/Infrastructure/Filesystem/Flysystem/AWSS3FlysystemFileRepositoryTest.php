<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Flysystem;

use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem\FlysystemFileRepository;
use Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem\FlysystemFileUrlResolver;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

/**
 * @group filesystem
 * @group aws_s3
 */
class AWSS3FlysystemFileRepositoryTest extends BaseIntegrationTestCase
{
    private FlysystemFileRepository $flysystemFileRepository;

    private FlysystemFileUrlResolver $flysystemFileUrlResolver;

    private File $file;

    private Media $media;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        if (!$_ENV['AWS_S3_ACCESS_KEY_ID']) {
            $this->markTestSkipped('AWS S3 is not configured.');
        }
        self::bootKernelResources(extraConfigResources: ['../config/aws_s3/*.php'], clearCache: true);
        $this->flysystemFileRepository  = $this->getService(FlysystemFileRepository::class);
        $this->flysystemFileUrlResolver = $this->getService(FlysystemFileUrlResolver::class);
        $media                          = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $this->media                    = $media;
        $this->file                     = $media->file();
    }

    public function testItShouldGetAWSAdapterConfiguration(): void
    {
        $this->assertSame('aws', self::getContainer()->getParameter('ranky_media_adapter'));
    }

    /**
     * @throws \Throwable
     */
    public function testItShouldWriteFileInToAWSS3(): void
    {
        $dummyFilePath = $this->getDummyDirectory().\DIRECTORY_SEPARATOR.$this->file->name();
        $this->flysystemFileRepository->write($dummyFilePath, $this->file->name());
        $this->assertTrue($this->flysystemFileRepository->exists($this->file->name()));
        $this->assertNotFalse(
            \file_get_contents(
                $this->flysystemFileUrlResolver->resolve($this->file->name())
            ),
            'file_get_contents should not return false'
        );
    }

    /**
     * @throws \Throwable
     * @throws \League\Flysystem\FilesystemException
     */
    public function testItShouldGetFileSize(): void
    {
        $fileSize = $this->flysystemFileRepository->filesize($this->file->name());
        $this->assertEquals($this->file->size(), $fileSize);
    }

    /**
     * @throws \Throwable
     * @throws \League\Flysystem\FilesystemException
     */
    public function testItShouldGetMimeType(): void
    {
        $mime = $this->flysystemFileRepository->mimeType($this->file->name());
        $this->assertEquals($this->file->mime(), $mime);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function testItShouldGetImageDimension(): void
    {
        if (!\str_contains($this->file->mime(), 'image/')) {
            $this->markTestSkipped('The media file is not an image.');
        }
        $dimension = $this->flysystemFileRepository->dimension($this->file->name());
        $this->assertEquals($this->media->dimension(), $dimension);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     * @throws \Throwable
     */
    public function testItShouldRenameFile(): string
    {
        $newFileName = 'test.jpg';
        $this->flysystemFileRepository->rename($this->file->name(), $newFileName);
        $this->assertTrue($this->flysystemFileRepository->exists($newFileName));
        $this->assertFalse($this->flysystemFileRepository->exists($this->file->name()));
        $this->assertNotFalse(
            \file_get_contents(
                $this->flysystemFileUrlResolver->resolve($newFileName)
            ),
            'file_get_contents should not return false'
        );

        return $newFileName;
    }

    /**
     * @throws \Throwable
     *
     * @depends testItShouldRenameFile
     */
    public function testItShouldDeleteFile(string $fileName): void
    {
        $this->flysystemFileRepository->delete($fileName);
        $this->assertFalse($this->flysystemFileRepository->exists($fileName));
    }
}
