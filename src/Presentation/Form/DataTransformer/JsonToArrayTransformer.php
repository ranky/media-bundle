<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class JsonToArrayTransformer implements DataTransformerInterface
{
    /**
     * Transforms Array To JSON.
     *
     * @throws \JsonException
     */
    public function transform(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException(
                \sprintf('Expected an array. %s type given', \gettype($value))
            );
        }

        return \json_encode($value, JSON_THROW_ON_ERROR);
    }

    /**
     * Transforms JSON to Array.
     *
     * @throws \JsonException
     * @return array<string,string>|null
     */
    public function reverseTransform(mixed $value): ?array
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }
}
