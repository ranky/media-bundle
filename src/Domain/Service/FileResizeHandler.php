<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Service;

use Ranky\MediaBundle\Domain\Contract\FileResize;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\SharedBundle\Domain\Service\ValidateHandlersTrait;
class FileResizeHandler
{
    use ValidateHandlersTrait;

    /** @var array<FileResize> */
    private readonly array $handlers;

    /**
     * @param iterable<FileResize> $handlers
     * @throws \Exception
     */
    public function __construct(iterable $handlers)
    {
        $this->handlers = $this->validateHandlers($handlers, FileResize::class);
    }

    public function resize(File $file, string $inputPath, string $outputPath, Dimension $dimension): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->support($file)) {
                return $handler->resize($inputPath, $outputPath, $dimension);
            }
        }

        return false;
    }

    public function support(File $file): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->support($file)) {
                return true;
            }
        }
        return false;
    }
}
