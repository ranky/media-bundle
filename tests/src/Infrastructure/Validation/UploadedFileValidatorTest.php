<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Validation;

use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Domain\Exception\UploadFileException;
use Ranky\MediaBundle\Infrastructure\Validation\UploadedFileValidator;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;

class UploadedFileValidatorTest extends BaseIntegrationTestCase
{
    public UploadedFileValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->getService(UploadedFileValidator::class);
    }

    public function testItShouldValidateUploadedFileRequest(): void
    {
        $uploadedFileRequest = new UploadedFileRequest(
            path: 'image.jpg',
            name: 'image.jpg',
            mime: 'image/jpg',
            extension: '.jpg',
            size: 1024
        );

        $this->validator->validate($uploadedFileRequest);

        $this->assertTrue(true);
    }

    public function testItShouldThrownExceptionWithMaxFileSize(): void
    {
        // default maxFileSize 7340032 (7mb)
        $uploadedFileRequest = new UploadedFileRequest(
            path: 'video.mp4',
            name: 'video.mp4',
            mime: 'video/mp4',
            extension: '.mp4',
            size: 7840032
        );

        $this->expectException(UploadFileException::class);
        $this->validator->validate($uploadedFileRequest);
    }
}
