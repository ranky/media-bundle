<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Exception;

use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

final class InvalidBreakpointException extends HttpDomainException
{
    public static function withName(string $breakpointName): self
    {
        return new self(\sprintf('Breakpoint %s is not valid', $breakpointName));
    }
}
