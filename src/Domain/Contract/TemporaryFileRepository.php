<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Contract;

interface TemporaryFileRepository extends FileRepository
{

    /**
     * Get the temporary file path
     *
     * @param string $path
     * @return string
     */
    public function temporaryFile(string $path): string;

}
