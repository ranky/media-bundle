<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Validation;

use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

final class UploadFileException extends HttpDomainException
{
    /**
     * @param string $message
     * @param string[] $errors
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, private readonly array $errors = [], int $code = 0, ?\Throwable $previous = null)
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
