<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Presentation\Behat;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;
use Ranky\MediaBundle\Application\CreateMedia\CreateMedia;
use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\MediaBundle\Tests\Dummy\User\Domain\UserRepository;
use Ranky\SharedBundle\Common\FileHelper;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;
use Ranky\SharedBundle\Presentation\Behat\BaseApiContext;


class MediaApiContext extends BaseApiContext
{

    private static string $mediaId = '01GGM122J0DA8NKJ49FW56RH7E';

    /** @AfterFeature */
    public static function cleanFeature(): void
    {
        /** @var string[] $configMediaBundle */
        $configMediaBundle = self::$container->getParameter('ranky_media');
        $uploadDirectory   = $configMediaBundle['upload_directory'];
        FileHelper::removeRecursiveDirectoriesAndFiles($uploadDirectory);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::$container->get(EntityManagerInterface::class);
        $purger        = new ORMPurger($entityManager);
        $purger->purge();
        $entityManager->close();
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
        /** @var UserRepository $userRepository */
        $userRepository = self::$container->get(UserRepository::class);
        $user           = $userRepository->getByUsername($username);
        if ($user) {
            $this->loginUser($user);
        }

        Assert::assertTrue($this->isGranted());
    }

}
