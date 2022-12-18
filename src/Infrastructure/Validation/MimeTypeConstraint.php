<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Validation;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class MimeTypeConstraint extends Constraint
{
    public string $message = 'The mime type of file is invalid ({{ type }}). Allowed mime types are {{ types }}.';

    /**
     * @param string[] $allowedMimeTypes
     * @param string|null $extension
     * @param $options
     * @param ?string[] $groups
     * @param $payload
     */
    public function __construct(
        private readonly array $allowedMimeTypes = [],
        private readonly ?string $extension = null,
        $options = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }

    /**
     * @return string[]
     */
    public function getAllowedMimeTypes(): array
    {
        return $this->allowedMimeTypes;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

}
