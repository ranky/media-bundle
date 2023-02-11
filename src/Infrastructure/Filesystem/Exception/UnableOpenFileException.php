<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Infrastructure\Filesystem\Exception;

use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

class UnableOpenFileException extends HttpDomainException
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
