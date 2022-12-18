<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Service;

use Ranky\MediaBundle\Domain\Contract\GenerateThumbnailsInterface;
use Ranky\MediaBundle\Domain\ValueObject\File;

class GenerateThumbnailsHandler
{
    /**
     * @var array<GenerateThumbnailsInterface>
     */
    private readonly array $handlers;

    /**
     * @param iterable<GenerateThumbnailsInterface> $handlers
     */
    public function __construct(iterable $handlers)
    {
        $this->handlers = $handlers instanceof \Traversable ? \iterator_to_array($handlers) : $handlers;
    }

    public function generate(string $mediaId, File $file): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof GenerateThumbnailsInterface && $handler->support($file)) {
                $handler->generate($mediaId);

                return true;
            }
        }

        return false;
    }

    public function support(File $file): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof GenerateThumbnailsInterface && $handler->support($file)) {
                return true;
            }
        }

        return false;
    }
}
