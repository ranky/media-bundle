<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

use Ranky\MediaBundle\Domain\ValueObject\Dimension;

interface FileRepository
{

    /**
     * Move a file from source to destination
     *
     * @param string $source
     * @param string $destination
     */
    public function write(string $source, string $destination): void;

    /**
     * Delete a file
     * If the directory does not exist, no action will be taken.
     *
     * @param string $path
     * @return void
     */
    public function delete(string $path): void;

    /**
     * Delete recursively a directory and all its content
     * If the directory does not exist, no action will be taken
     *
     * @param string $path
     * @return void
     */
    public function deleteDirectory(string $path): void;

    /**
     * Rename/Move a file
     *
     * @param string $oldPathFileName
     * @param string $newPathFileName
     * @return void
     */
    public function rename(string $oldPathFileName, string $newPathFileName): void;

    /**
     * Copy a file from source to destination
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    public function copy(string $source, string $destination): void;

    /**
     * Get the file size in bytes
     * If not found, return 0
     *
     * @param string $path
     * @return int
     */
    public function filesize(string $path): int;

    /**
     * Get the file mime type
     *
     * @param string $path
     * @return string
     */
    public function mimeType(string $path): string;

    /**
     * Check if a file or directory exists
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Get the file dimension (width and height)
     *
     * @param string $path
     * @param string|null $mime
     * @return Dimension
     */
    public function dimension(string $path, string $mime = null): Dimension;

    /**
     * Create a directory
     *
     * @param string $path
     * @return void
     */
    public function makeDirectory(string $path): void;

}
