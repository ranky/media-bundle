<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Infrastructure\Validation;

use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Infrastructure\Validation\MimeTypeConstraint;
use Ranky\MediaBundle\Infrastructure\Validation\MimeTypeConstraintValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @covers \Ranky\MediaBundle\Infrastructure\Validation\MimeTypeConstraintValidator
 * @covers \Ranky\MediaBundle\Infrastructure\Validation\MimeTypeConstraint
 *
 * @extends ConstraintValidatorTestCase<MimeTypeConstraintValidator>
 */
class MimeConstraintValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): MimeTypeConstraintValidator
    {
        return new MimeTypeConstraintValidator();
    }

    /**
     * @dataProvider dataProviderInvalidValues
     * @param string[] $allowedMimeTypes
     * @param string $value
     * @param string $extension
     *
     * @return void
     */
    public function testItShouldFailValidation(array $allowedMimeTypes, string $value, string $extension): void
    {
        $mimeTypeConstraint = new MimeTypeConstraint(
            allowedMimeTypes: $allowedMimeTypes,
            extension: $extension
        );
        $this->validator->validate($value, $mimeTypeConstraint);

        $this->buildViolation($mimeTypeConstraint->message)
            ->setParameter('{{ type }}', '"'.$value.'"')
            ->setParameter('{{ types }}', implode(', ', $allowedMimeTypes))
            ->assertRaised();
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function dataProviderInvalidValues(): array
    {
        return [
            [['video/*'], 'image/png', 'png'],
            [['image/png'], 'image/jpg', 'jpg'],
            [['.png'], 'image/jpg', 'jpg'],
            [['video/mpeg'], 'video/mp4', 'mp4'],
        ];
    }

    public function testAnyStringValueIsValid(): void
    {
        $this->validator->validate('random', new MimeTypeConstraint([]));
        $this->assertNoViolation();
    }


    public function testNullIsInvalid(): void
    {
        $mimeTypeConstraint = new MimeTypeConstraint(allowedMimeTypes: ['image/*'], extension: 'jpg');
        $this->validator->validate(null, $mimeTypeConstraint);
        $this->buildViolation($mimeTypeConstraint->message)
            ->setParameter('{{ type }}', 'null')
            ->setParameter('{{ types }}', implode(', ', $mimeTypeConstraint->getAllowedMimeTypes()))
            ->assertRaised();
    }

    public function testEmptyStringIsInvalid(): void
    {
        $mimeTypeConstraint = new MimeTypeConstraint(allowedMimeTypes: ['image/*']);
        $this->validator->validate('', $mimeTypeConstraint);
        $this->buildViolation($mimeTypeConstraint->message)
            ->setParameter('{{ type }}', '""')
            ->setParameter('{{ types }}', implode(', ', $mimeTypeConstraint->getAllowedMimeTypes()))
            ->assertRaised();
    }

    public function testItShouldValidateMime(): void
    {
        $this->validator->validate(
            MimeType::IMAGE->value,
            new MimeTypeConstraint(allowedMimeTypes: ['image/*'], extension: 'jpg')
        );
        $this->assertNoViolation();
    }

    public function testItShouldValidateMimeType(): void
    {
        $this->validator->validate('image/*', new MimeTypeConstraint(allowedMimeTypes: ['image/*'], extension: 'jpg'));
        $this->assertNoViolation();
    }

    public function testItShouldValidateMimeSubType(): void
    {
        $this->validator->validate('.jpg', new MimeTypeConstraint());
        $this->assertNoViolation();
    }

}
