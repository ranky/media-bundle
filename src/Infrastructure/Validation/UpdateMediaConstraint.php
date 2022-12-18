<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Validation;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class UpdateMediaConstraint
{

    /**
     * @return array<string, Constraint|array<Constraint>>
     */
    public function __invoke(): array
    {
        return [
            'id' => new NotBlank(),
            'name' => [new NotBlank(), new Length(max: 200)],
            'alt' => [new NotBlank(), new Length(max: 255)],
            'title' => [new NotBlank(), new Length(max: 255)],
        ];
    }

}
