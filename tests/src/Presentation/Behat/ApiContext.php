<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Presentation\Behat;

use PHPUnit\Framework\Assert;
use Ranky\MediaBundle\Application\CreateMedia\CreateMedia;
use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\MediaBundle\Tests\Dummy\User\Domain\UserRepositoryInterface;
use Ranky\SharedBundle\Common\FileHelper;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;
use Ranky\SharedBundle\Presentation\Behat\AbstractApiContext;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ApiContext extends AbstractApiContext
{
    use ApiContextTrait;

    private static string $mediaId = '01GGM122J0DA8NKJ49FW56RH7E';


    /** @AfterFeature */
    public static function cleanFeature(): void
    {
        /** @var string[] $configMediaBundle */
        $configMediaBundle = self::$container->getParameter('ranky_media');
        $uploadDirectory   = $configMediaBundle['upload_directory'];
        FileHelper::removeRecursiveDirectoriesAndFiles($uploadDirectory);

        // alternative https://symfonycasts.com/screencast/phpunit-legacy/control-database
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $application->run(
            new ArrayInput([
                'command' => 'doctrine:database:drop',
                '--if-exists' => true,
                '--force' => true,
            ])
        );
    }

    /** @BeforeFeature
     * @throws \Exception
     */
    public static function prepareFeature(): void
    {
        $mediaId             = MediaId::fromString(self::$mediaId);
        $media               = MediaFactory::random(MimeType::IMAGE, 'png', $mediaId);
        $tmpFilePath         = self::getTmpPathForUpload($media->file()->name());
        $uploadedFileRequest = new UploadedFileRequest(
            $tmpFilePath,
            $media->file()->name(),
            $media->file()->mime(),
            $media->file()->extension(),
            $media->file()->size()
        );

        $userIdentifier = UserIdentifier::fromString('jcarlos');
        /** @var CreateMedia $createMedia */
        $createMedia = self::$container->get(CreateMedia::class);

        $createMedia->__invoke(
            $uploadedFileRequest,
            $userIdentifier->value(),
            $mediaId->asString()
        );
    }

    /**
     * @Given I am logged in as :username
     * @throws \Exception
     */
    public function iAmLoggedInAs(string $username): void
    {
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = self::$container->get(UserRepositoryInterface::class);
        $user           = $userRepository->getByUsername($username);
        if ($user) {
            $this->loginUser($user);
        }

        Assert::assertTrue($this->isGranted());
    }

    /**
     * @Given I attach the file :fileName to request with key :key
     */
    public function iAttachFileToRequest(string $fileName, string $key): void
    {
        $tmpFilePath       = self::getTmpPathForUpload($fileName);
        $uploadedFile      = new UploadedFile(
            $tmpFilePath,
            $fileName,
            \mime_content_type($tmpFilePath) ?: null
        );
        $this->files[$key] = $uploadedFile;
    }

}
