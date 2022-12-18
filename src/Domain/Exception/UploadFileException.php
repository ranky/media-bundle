<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Exception;

use Ranky\SharedBundle\Domain\Exception\HttpDomainException;
use Throwable;

final class UploadFileException extends HttpDomainException
{
    /**
     * @param string $message
     * @param string[] $errors
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, private readonly array $errors = [], int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, self::DEFAULT_STATUS_CODE, $code, $previous);
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

}
