<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Application;

use Ranky\MediaBundle\Application\CreateMedia\CreateMedia;
use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\FindMedia\FindMediaByIds;
use Ranky\MediaBundle\Application\FindMedia\FindMediaByPaths;
use Ranky\MediaBundle\Application\GetMedia\GetMediaByFilePath;
use Ranky\MediaBundle\Application\GetMedia\GetMediaById;
use Ranky\MediaBundle\Application\MediaService;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Tests\BaseUnitTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;

final class MediaServiceTest extends BaseUnitTestCase
{
    public function testItUploadsFileFromPathWithoutMovingTheSourceFile(): void
    {
        $temporaryPath = \tempnam(\sys_get_temp_dir(), 'ranky-media-service-test-');
        self::assertNotFalse($temporaryPath);
        $sourcePath = $temporaryPath.'.txt';
        self::assertTrue(\rename($temporaryPath, $sourcePath));
        \file_put_contents($sourcePath, 'fixture media');

        $media = MediaFactory::random(MimeType::TEXT, 'txt');
        $response = MediaResponse::fromMedia($media, '/uploads', 'jcarlos', 'jcarlos');
        $temporaryUploadPath = null;
        $createMedia = $this->createMock(CreateMedia::class);
        $createMedia
            ->expects(self::once())
            ->method('__invoke')
            ->with(
                $this->callback(function (UploadedFileRequest $request) use (&$temporaryUploadPath, $sourcePath): bool {
                    $temporaryUploadPath = $request->path();

                    self::assertSame(\basename($sourcePath), $request->name());
                    self::assertSame('text/plain', $request->mime());
                    self::assertSame('txt', $request->extension());
                    self::assertSame(13, $request->size());
                    self::assertNotSame($sourcePath, $request->path());
                    self::assertFileExists($request->path());
                    self::assertFileExists($sourcePath);

                    return true;
                }),
                'jcarlos',
            )
            ->willReturn($response);

        try {
            $mediaService = new MediaService(
                $createMedia,
                $this->createMock(GetMediaByFilePath::class),
                $this->createMock(FindMediaByPaths::class),
                $this->createMock(GetMediaById::class),
                $this->createMock(FindMediaByIds::class),
                $this->createMock(MediaToResponseTransformer::class),
            );

            self::assertSame($response, $mediaService->uploadFileFromPath($sourcePath, 'jcarlos'));
            self::assertFileExists($sourcePath);
            self::assertNotNull($temporaryUploadPath);
            self::assertFileDoesNotExist($temporaryUploadPath);
        } finally {
            if (\file_exists($sourcePath)) {
                \unlink($sourcePath);
            }
            if (\file_exists($temporaryPath)) {
                \unlink($temporaryPath);
            }
        }
    }

    public function testItRejectsMissingFilePath(): void
    {
        $mediaService = new MediaService(
            $this->createMock(CreateMedia::class),
            $this->createMock(GetMediaByFilePath::class),
            $this->createMock(FindMediaByPaths::class),
            $this->createMock(GetMediaById::class),
            $this->createMock(FindMediaByIds::class),
            $this->createMock(MediaToResponseTransformer::class),
        );

        $this->expectException(\InvalidArgumentException::class);

        $mediaService->uploadFileFromPath('/missing/media.txt');
    }
}
