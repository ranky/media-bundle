<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Exception;

use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

final class NotFoundMediaException extends HttpDomainException
{

    public static function withId(string $id): self
    {
        return new self(\sprintf('No media found with ID "%s"', $id), 404);
    }
    public static function withFileName(string $fileName): self
    {
        return new self(\sprintf('No media found with file name "%s"', $fileName), 404);
    }
}
