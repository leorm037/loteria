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

use App\Entity\Bolao;
use App\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BolaoVoter extends Voter
{
    public const EDIT = 'BOLAO_EDIT';
    public const DOWNLOAD = 'BOLAO_DOWNLOAD';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [
                    self::EDIT, self::DOWNLOAD,
                ]) && $subject instanceof Bolao;
    }

    /**
     * @param Bolao $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user, $subject);
            case self::DOWNLOAD:
                return $this->canDownload($user, $subject);
        }

        return false;
    }

    private function canEdit(Usuario $user, Bolao $bolao): bool
    {
        return $user === $bolao->getUsuario();
    }

    private function canDownload(Usuario $user, Bolao $bolao): bool
    {
        return $user === $bolao->getUsuario();
    }
}
