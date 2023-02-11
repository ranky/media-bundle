<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Exception;

use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

final class InvalidPathException extends HttpDomainException
{
    public static function withUrl(string $pathUrl): self
    {
        return new self(\sprintf('Invalid path provided: %s cannot be a URL.', $pathUrl));
    }
}
