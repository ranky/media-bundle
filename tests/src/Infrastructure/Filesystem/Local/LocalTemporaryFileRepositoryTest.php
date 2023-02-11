<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Local;

use Ranky\MediaBundle\Domain\Contract\TemporaryFileRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalTemporaryFileRepository;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class LocalTemporaryFileRepositoryTest extends BaseIntegrationTestCase
{
    private TemporaryFileRepository $temporaryFileRepository;

    private File $file;

    private Media $media;
    private string $dummyFilePath;
    private string $temporaryFilePath;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->temporaryFileRepository = $this->getService(LocalTemporaryFileRepository::class);
        $this->media = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $this->file  = $this->media->file();
        $this->dummyFilePath = $this->getDummyDirectory().'/'.$this->file->path();
        $this->temporaryFilePath  = $this->temporaryFileRepository->temporaryFile($this->file->path());
    }

    public function testItShouldCopyFile(): void
    {
        copy($this->dummyFilePath, $this->temporaryFilePath);
        $this->assertFileExists($this->temporaryFilePath);
        $this->temporaryFileRepository->copy($this->file->path(), $this->getTempFileDirectory().'/'.$this->file->path());
        $this->assertFileExists( $this->getTempFileDirectory().'/'.$this->file->path());
    }

    public function testItShouldGetFilesize(): void
    {
        $this->assertEquals(
            $this->file->size(),
            $this->temporaryFileRepository->filesize($this->file->path())
        );
    }

    public function testItShouldGetMimeType(): void
    {
        $this->assertEquals(
            $this->file->mime(),
            $this->temporaryFileRepository->mimeType($this->file->path())
        );
    }

    public function testItShouldGetDimension(): void
    {
        $this->assertEquals(
            $this->media->dimension(),
            $this->temporaryFileRepository->dimension($this->file->path())
        );
    }

    public function testItShouldRenameUploadFile(): string
    {
        $newFileName   = 'rename.'.\pathinfo($this->temporaryFilePath, \PATHINFO_EXTENSION);
        $newOutputPath = $this->temporaryFileRepository->temporaryFile($newFileName);
        $this->temporaryFileRepository->rename($this->temporaryFilePath, $newOutputPath);
        $this->assertFileExists($newOutputPath);

        return $newOutputPath;
    }

    /**
     * @depends testItShouldRenameUploadFile
     */
    public function testItShouldDeleteRenameFile(string $newOutputPath): void
    {
        $this->temporaryFileRepository->delete($newOutputPath);
        $this->assertFileDoesNotExist($newOutputPath);
    }
}
