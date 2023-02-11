<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Service;

use Ranky\MediaBundle\Domain\Contract\FileCompress;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\SharedBundle\Domain\Service\ValidateHandlersTrait;

class FileCompressHandler
{
    use ValidateHandlersTrait;

     /** @var array<FileCompress> */
    private readonly array $handlers;

    /**
     * @param iterable<FileCompress> $handlers
     * @throws \Exception
     */
    public function __construct(iterable $handlers)
    {
        $this->handlers = $this->validateHandlers($handlers, FileCompress::class);
    }

    public function compress(File $file, string $path): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->support($file)) {
                $handler->compress($path);
                return true;
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
