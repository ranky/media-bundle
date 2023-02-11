<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Filesystem\Exception;

use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

final class WriteTemporaryFileToOriginException extends HttpDomainException
{
    public function __construct(string $message = 'Could not write temporary file to origin', \Throwable $previous = null)
    {
        parent::__construct($message, self::DEFAULT_STATUS_CODE, 0, $previous);
    }

}
