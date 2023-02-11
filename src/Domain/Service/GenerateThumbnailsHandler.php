<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Service;

use Ranky\MediaBundle\Domain\Contract\GenerateThumbnails;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\SharedBundle\Domain\Service\ValidateHandlersTrait;

class GenerateThumbnailsHandler
{
    use ValidateHandlersTrait;

    /**
     * @var array<GenerateThumbnails>
     */
    private readonly array $handlers;

    /**
     * @param iterable<GenerateThumbnails> $handlers
     * @throws \Exception
     */
    public function __construct(iterable $handlers)
    {
        $this->handlers = $this->validateHandlers($handlers, GenerateThumbnails::class);
    }

    public function generate(string $mediaId, File $file, Dimension $dimension): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->support($file)) {
                $handler->generate($mediaId, $file, $dimension);

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
