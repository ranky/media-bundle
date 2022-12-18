<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;

interface FileRepositoryInterface
{
    public function upload(UploadedFileRequest $uploadedFileRequest): File;

    public function delete(string $fileName): void;

    public function rename(string $oldPathFileName, string $newPathFileName): void;

    public function filesizeFromPath(string $path): int;

    public function dimensionsFromPath(string $path, string $mime = null): Dimension;

    public function makeDirectory(string $path): void;

}
