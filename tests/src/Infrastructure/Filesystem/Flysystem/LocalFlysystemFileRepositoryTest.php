<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Flysystem;

use Ranky\MediaBundle\Domain\Contract\FileRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem\FlysystemFileRepository;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class LocalFlysystemFileRepositoryTest extends BaseIntegrationTestCase
{
    private FileRepository $flysystemFileRepository;

    private File $file;

    private Media $media;
    private string $dummyFilePath;
    private string $uploadedFilePath;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        self::bootKernelResources([], true);
        $this->flysystemFileRepository = $this->getService(FlysystemFileRepository::class);
        $media                         = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $this->media                   = $media;
        $this->file                    = $media->file();
        $this->dummyFilePath           = $this->getDummyDirectory().'/'.$this->file->path();
        $this->uploadedFilePath        = $this->getPublicUploadDirectory().'/'.$this->file->path();
    }

    public function testItShouldGetLocalAdapterConfiguration(): void
    {
        $this->assertSame('local', self::getContainer()->getParameter('ranky_media_adapter'));
    }

    /**
     * @throws \Throwable
     */
    public function testItShouldWriteFileInToPublicDirectory(): void
    {
        $this->flysystemFileRepository->write($this->dummyFilePath, $this->file->path());

        $this->assertTrue($this->flysystemFileRepository->exists($this->file->path()));
        $this->assertFileExists($this->uploadedFilePath);
    }

    /**
     * @throws \Throwable
     * @throws \League\Flysystem\FilesystemException
     */
    public function testItShouldGetFileSize(): void
    {
        $fileSize = $this->flysystemFileRepository->filesize($this->file->path());
        $this->assertEquals($this->file->size(), $fileSize);
    }

    /**
     * @throws \Throwable
     * @throws \League\Flysystem\FilesystemException
     */
    public function testItShouldGetMimeType(): void
    {
        $mime = $this->flysystemFileRepository->mimeType($this->file->path());
        $this->assertEquals($this->file->mime(), $mime);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function testItShouldGetImageDimension(): void
    {
        $dimension = $this->flysystemFileRepository->dimension($this->file->path());
        $this->assertEquals($this->media->dimension(), $dimension);
    }

    /**
     * @throws \Throwable
     */
    public function testItShouldCreateAndRemoveDirectory(): void
    {
        $this->flysystemFileRepository->makeDirectory('/test/');
        $this->assertTrue($this->flysystemFileRepository->exists('/test/'));
        $this->flysystemFileRepository->deleteDirectory('/test/');
        $this->assertFalse($this->flysystemFileRepository->exists('/test/'));
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     * @throws \Throwable
     */
    public function testItShouldRenameFile(): string
    {
        $newFileName = 'test.jpg';
        $this->flysystemFileRepository->rename($this->file->path(), $newFileName);
        $this->assertTrue($this->flysystemFileRepository->exists($newFileName));
        $this->assertFalse($this->flysystemFileRepository->exists($this->file->path()));
        $this->assertFileExists($this->getPublicUploadDirectory().'/'.$newFileName);

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
        $this->assertFileDoesNotExist($this->getPublicUploadDirectory().'/'.$fileName);
    }
}
