<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ArrayValueNotRepeatValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /* @var App\Validator\ArrayValueNotRepeat $constraint */
        if (!$constraint instanceof ArrayValueNotRepeat) {
            throw new \InvalidArgumentException('Constraint is expected to be an instance of ArrayValueNotRepeat.');
        }

        $count = array_count_values($value);

        $repeat = [];

        foreach ($count as $item => $amount) {
            if ($amount > 1) {
                $repeat[] = $item;
            }
        }

        if (\count($repeat) > 0) {
            $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', implode(', ', $value))
                    ->addViolation();
        }
    }
}
