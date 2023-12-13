<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Security\Voter;

use App\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserEmailIsVerifiedVoter extends Voter
{
    public const EMAIL_IS_VERIFIED = 'EMAIL_IS_VERIFIED';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EMAIL_IS_VERIFIED]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Usuario $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EMAIL_IS_VERIFIED:
                return $user->isVerified();
        }

        return false;
    }
}
