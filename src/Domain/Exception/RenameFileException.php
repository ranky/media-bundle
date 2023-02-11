<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Exception;

use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

final class RenameFileException extends HttpDomainException
{
    /**
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, self::DEFAULT_STATUS_CODE, 0, $previous);
    }

}
