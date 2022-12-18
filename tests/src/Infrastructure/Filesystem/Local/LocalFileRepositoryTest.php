<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Filesystem\Local;

use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Domain\Contract\FilePathResolverInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalFilePathResolver;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalFileRepository;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

class LocalFileRepositoryTest extends BaseIntegrationTestCase
{
    private FileRepositoryInterface $fileRepository;
    private FilePathResolverInterface $filePathResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileRepository   = $this->getService(LocalFileRepository::class);
        $this->filePathResolver = $this->getService(LocalFilePathResolver::class);
    }

    public function testItShouldUploadFileToPublicUploadDirectory(): string
    {
        $media = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $file  = $media->file();

        $inputPath      = $this->getDummyDir().'/'.$file->name();
        $actualFileName = $file->baseName().'_upload.'.$file->extension();
        $tmpPath        = $this->getTempFileDir().'/'.$actualFileName;
        copy($inputPath, $tmpPath);

        $uploadedFileRequest = new UploadedFileRequest(
            $tmpPath,
            $actualFileName,
            $file->mime(),
            $file->extension(),
            $file->size()
        );

        $actualFile = $this->fileRepository->upload($uploadedFileRequest);
        $outputPath = $this->filePathResolver->resolve($actualFile->path());

        $this->assertFileExists($outputPath);

        return $outputPath;
    }

    /**
     * @depends testItShouldUploadFileToPublicUploadDirectory
     */
    public function testItShouldRenameUploadFile(string $outputPath): string
    {
        $newFileName = 'rename.'.\pathinfo($outputPath, \PATHINFO_EXTENSION);
        $newOutputPath = $this->filePathResolver->resolve($newFileName);
        $this->fileRepository->rename($outputPath, $newOutputPath);
        $this->assertFileExists($newOutputPath);

        return $newFileName;
    }

    /**
     * @depends testItShouldRenameUploadFile
     */
    public function testItShouldDeleteRenameFile(string $newFileName): void
    {
        $this->fileRepository->delete($newFileName);
        $this->assertFileDoesNotExist( $this->filePathResolver->resolve($newFileName));
    }


}
