<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem;

use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Ranky\MediaBundle\Domain\Contract\FileRepository;
use Ranky\MediaBundle\Domain\Contract\TemporaryFileRepository;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Infrastructure\Filesystem\Exception\UnableOpenFileException;

class FlysystemFileRepository implements FileRepository
{
    public function __construct(
        private readonly TemporaryFileRepository $temporaryFileRepository,
        private readonly FilesystemOperator $rankyMediaStorage,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function write(string $source, string $destination): void
    {
        try {
            $stream = \fopen($source, 'rb+');
            if(!$stream) {
                throw new UnableOpenFileException(
                    \sprintf('Unable to open stream file %s for writing', $stream)
                );
            }
            $this->rankyMediaStorage->writeStream($destination, $stream);
            \fclose($stream);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), [
                'source'      => $source,
                'destination' => $destination,
            ]);
            throw $exception;
        }
    }

    /**
     * @throws \League\Flysystem\FilesystemException | \Throwable
     */
    public function delete(string $path): void
    {
        if (!$this->exists($path)) {
            return;
        }
        try {
            $this->rankyMediaStorage->delete($path);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), [
                'path' => $path,
            ]);
            throw $exception;
        }
    }

    /**
     * @throws \League\Flysystem\FilesystemException | \Throwable
     */
    public function deleteDirectory(string $path): void
    {
        if (!$this->exists($path)) {
            return;
        }
        try {
            $this->rankyMediaStorage->deleteDirectory($path);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), [
                'path' => $path,
            ]);
            throw $exception;
        }
    }

    /**
     * @throws \League\Flysystem\FilesystemException|\Throwable
     */
    public function rename(string $oldPathFileName, string $newPathFileName): void
    {
        try {
            $this->rankyMediaStorage->move($oldPathFileName, $newPathFileName);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), [
                'oldPathFileName' => $oldPathFileName,
                'newPathFileName' => $newPathFileName,
            ]);
            throw $exception;
        }
    }

    /**
     * @throws \League\Flysystem\FilesystemException|\Throwable
     */
    public function filesize(string $path): int
    {
        try {
            return $this->rankyMediaStorage->fileSize($path);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), [
                'path' => $path,
            ]);
            throw $exception;
        }
    }

    /**
     * @throws \League\Flysystem\FilesystemException | UnableOpenFileException
     */
    public function dimension(string $path, string $mime = null): Dimension
    {
        $mime   ??= $this->mimeType($path);
        $stream = $this->rankyMediaStorage->readStream($path);

        $temporaryFile = $this->temporaryFileRepository->temporaryFile($path);
        $localStream   = \fopen($temporaryFile, 'wb+');
        if(!$localStream) {
            throw new UnableOpenFileException(
                \sprintf('Unable to open temporary stream file %s', $temporaryFile)
            );
        }
        \stream_copy_to_stream($stream, $localStream);
        \fclose($localStream);
        \fclose($stream);

        $dimension = $this->temporaryFileRepository->dimension($temporaryFile, $mime);
        $this->temporaryFileRepository->delete($temporaryFile);

        return $dimension;
    }


    /**
     * @throws \League\Flysystem\FilesystemException|\Throwable
     */
    public function copy(string $source, string $destination): void
    {
        try {
            $this->rankyMediaStorage->copy($source, $destination);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), [
                'source'      => $source,
                'destination' => $destination,
            ]);
            throw $exception;
        }
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function mimeType(string $path): string
    {
        return $this->rankyMediaStorage->mimeType($path);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function exists(string $path): bool
    {
        return $this->rankyMediaStorage->has($path);
    }


    /**
     * @throws \League\Flysystem\FilesystemException|\Throwable
     */
    public function makeDirectory(string $path): void
    {
        if ($this->exists($path)) {
            return;
        }
        try {
            $this->rankyMediaStorage->createDirectory($path);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), [
                'path' => $path,
            ]);
            throw $exception;
        }
    }
}
