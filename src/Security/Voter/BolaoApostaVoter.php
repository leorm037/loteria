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

use App\Entity\Aposta;
use App\Entity\Bolao;
use App\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BolaoApostaVoter extends Voter
{
    public const NEW = 'BOLAO_APOSTA_NEW';
    public const EDIT = 'BOLAO_APOSTA_EDIT';
    public const DELETE = 'BOLAO_APOSTA_DELETE';
    public const IMPORT = 'BOLAO_APOSTA_IMPORT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::NEW, self::EDIT, self::DELETE, self::IMPORT]) && ($subject instanceof Aposta || $subject instanceof Bolao);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /**
         * @var Usuario $user
         * @var Aposta  $subject
         */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::NEW:
                return $this->canNew($subject, $user);
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
            case self::IMPORT:
                return $this->canImport($subject, $user);
        }

        return false;
    }

    private function canNew(Bolao $bolao, Usuario $usuario): bool
    {
        return $usuario === $bolao->getUsuario();
    }

    private function canEdit(Aposta $aposta, Usuario $usuario): bool
    {
        return $usuario === $aposta->getBolao()->getUsuario() && !$aposta->isIsConferido();
    }

    private function canDelete(Aposta $aposta, Usuario $usuario): bool
    {
        return $usuario === $aposta->getBolao()->getUsuario() && !$aposta->isIsConferido();
    }

    private function canImport(Bolao $bolao, Usuario $usuario): bool
    {
        return $usuario === $bolao->getUsuario();
    }
}
