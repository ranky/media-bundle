<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Filesystem\Local;

use Psr\Log\LoggerInterface;
use Ranky\MediaBundle\Domain\Contract\FilePathResolver;
use Ranky\MediaBundle\Domain\Contract\FileRepository;
use Ranky\MediaBundle\Domain\Exception\RenameFileException;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Infrastructure\Filesystem\Exception\CopyFileException;
use Ranky\SharedBundle\Common\FileHelper;

class LocalFileRepository implements FileRepository
{
    public function __construct(
        private readonly FilePathResolver $filePathResolver,
        private readonly LoggerInterface $logger
    ) {
    }

    public function write(string $source, string $destination): void
    {
        $this->copy($source, $destination);
    }

    public function copy(string $source, string $destination): void
    {
        $source = $this->filePathResolver->resolve($source);

        if (!$this->exists($source)) {
            throw new CopyFileException(
                \sprintf(
                    'File did not find when trying to copy the file %s',
                    $source
                )
            );
        }
        $this->makeDirectory($destination);

        if (!\copy($source, $destination)) {
            throw new CopyFileException(
                \sprintf('Could not copy the file from "%s" to "%s".', $source, $destination)
            );
        }
    }

    public function rename(string $oldPathFileName, string $newPathFileName): void
    {
        $oldPath = $this->filePathResolver->resolve($oldPathFileName);
        $newPath = $this->filePathResolver->resolve($newPathFileName);


        if (!$this->exists($oldPath)) {
            throw new RenameFileException(
                \sprintf(
                    'File did not find when trying to rename the file %s',
                    $oldPath
                )
            );
        }
        if (!\rename($oldPath, $newPath)) {
            throw new RenameFileException(
                \sprintf('Could not move (rename) the file "%s" to "%s".', $oldPath, $newPath)
            );
        }
    }

    public function delete(string $path): void
    {
        $path = $this->filePathResolver->resolve($path);
        if (!$this->exists($path)) {
            $this->logger->warning(
                'The specified file could not be found while attempting to delete it"',
                ['file' => $path]
            );

            return;
        }
        if (!\unlink($path)) {
            $this->logger->error('File did not delete', ['file' => $path]);
        }
    }

    public function deleteDirectory(string $path): void
    {
        $path = $this->filePathResolver->resolve($path);
        if (!$this->exists($path)) {
            $this->logger->warning(
                'The specified directory could not be found while attempting to delete it',
                ['directory' => $path]
            );

            return;
        }
        FileHelper::removeRecursiveDirectoriesAndFiles(\dirname($path));
    }

    /**
     * @param string $path
     * @return int<0, max>
     */
    public function filesize(string $path): int
    {
        $path = $this->filePathResolver->resolve($path);

        if ($this->exists($path) && $size = \filesize($path)) {
            return $size;
        }
        $this->logger->warning('The file size could not be obtained. "0" returned', ['file' => $path]);

        return 0;
    }

    public function dimension(string $path, string $mime = null): Dimension
    {
        $path = $this->filePathResolver->resolve($path);

        $mime ??= $this->mimeType($path);

        if ($mime && !\str_contains($mime, 'image/')) {
            return new Dimension();
        }
        if (\is_array($dimensions = @getimagesize($path))) {
            return new Dimension($dimensions[0], $dimensions[1]);
        }

        return new Dimension();
    }

    public function mimeType(string $path): string
    {
        $path = $this->filePathResolver->resolve($path);

        return \mime_content_type($path) ?: '';
    }

    public function exists(string $path): bool
    {
        $path = $this->filePathResolver->resolve($path);

        return \file_exists($path);
    }

    public function makeDirectory(string $path): void
    {
        $path = $this->filePathResolver->resolve($path);
        FileHelper::makeDirectory(\dirname($path));
    }
}
