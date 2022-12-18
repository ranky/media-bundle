<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Presentation\Behat;

trait ApiMediaContextTrait
{
    public static function getTmpPathForUpload(string $dummyFileName): string
    {
        $dummyFilePath = self::$kernel->getProjectDir().'/dummy/'.$dummyFileName;
        $tmpFilePath   = self::$kernel->getCacheDir().'/'.$dummyFileName;
        if (!copy($dummyFilePath, $tmpFilePath)) {
            throw new \RuntimeException(
                sprintf(
                    'The file %s could not be copied. Check the file exists or the permissions of the directory %s',
                    $dummyFilePath,
                    self::$kernel->getCacheDir()
                )
            );
        }
        return $tmpFilePath;
    }

}
