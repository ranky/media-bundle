<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Infrastructure\Persistence\Dql\Sqlite;

/**
 * https://github.com/beberlei/DoctrineExtensions
 */
class Year extends NumberFromStrfTime
{
    protected function getFormat(): string
    {
        return '%Y';
    }
}
