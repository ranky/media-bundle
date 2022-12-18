<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MimeTypeConstraintValidator extends ConstraintValidator
{

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MimeTypeConstraint) {
            throw new UnexpectedTypeException($constraint, MimeTypeConstraint::class);
        }

        if (empty($constraint->getAllowedMimeTypes())) {
            return;
        }

        if ($value === null || $value === '' || !$this->isValidMimeType(
            $value,
            $constraint->getExtension(),
            $constraint->getAllowedMimeTypes()
        )
        ) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ type }}', $this->formatValue($value))
                ->setParameter('{{ types }}', implode(', ', $constraint->getAllowedMimeTypes()))
                ->addViolation();
        }
    }

    /**
     * @param string $currentMime
     * @param ?string $extension
     * @param string[] $mimeTypes
     *
     * @return bool
     */
    private function isValidMimeType(string $currentMime, ?string $extension, array $mimeTypes): bool
    {
        foreach ($mimeTypes as $mime) {
            // is mime format image/*
            if (\str_contains($mime, '/')) {
                $mimePattern = \addcslashes($mime, '/');
                if (\preg_match('/'.$mimePattern.'/', $currentMime)) {
                    return true;
                }
                continue;
            }
            if (!$extension){
                continue;
            }
            $normalizeExtension = static fn($extension) => \mb_strtolower(ltrim($extension, '.'));
            // is mime extension (.jpg)
            if (\str_contains($mime, '.')
                && $normalizeExtension($mime) === $normalizeExtension($extension)) {
                return true;
            }
        }

        return false;
    }

}
