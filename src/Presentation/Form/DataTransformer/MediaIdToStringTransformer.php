<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Presentation\Form\DataTransformer;

use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MediaIdToStringTransformer implements DataTransformerInterface
{
    /**
     * Transforms a MediaId(Ulid) object into a string.
     *
     */
    public function transform(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof MediaId) {
            throw new TransformationFailedException(
                \sprintf(
                    'Expected a MediaId. %s type given',
                    gettype($value)
                )
            );
        }

        return (string)$value;
    }

    /**
     * Transforms a MediaId(ULID) string into a MediaId object.
     *
     */
    public function reverseTransform(mixed $value): ?MediaId
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!\is_string($value)) {
            throw new TransformationFailedException(\sprintf('Expected a string. %s type given', gettype($value)));
        }

        try {
            return MediaId::fromString($value);
        } catch (\Throwable $e) {
            throw new TransformationFailedException(
                sprintf('The value "%s" is not a valid MediaId ULID.', $value),
                $e->getCode(),
                $e
            );
        }
    }
}
