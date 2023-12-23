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

class BolaoApostadorVoter extends Voter
{
    public const NEW = 'BOLAO_APOSTADOR_NEW';
    public const DELETE = 'BOLAO_APOSTADOR_DELETE';
    public const LIST = 'BOLAO_APOSTADOR_LIST';
    public const EDIT = 'BOLAO_APOSTADOR_EDIT';
    public const DOWNLOAD = 'BOLAO_APOSTADOR_DOWNLOAD';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [
                        self::NEW, self::LIST, self::EDIT, self::DOWNLOAD, self::DELETE,
                ]) && $subject instanceof Bolao;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user, $subject);
            case self::LIST:
                return $this->canList($user, $subject);
            case self::NEW:
                return $this->canNew($user, $subject);
            case self::DOWNLOAD:
                return $this->canDownload($user, $subject);
            case self::DELETE:
                return $this->canDelete($user, $subject);
        }

        return false;
    }

    private function canNew(Usuario $user, Bolao $bolao): bool
    {
        return $user === $bolao->getUsuario();
    }

    private function canEdit(Usuario $user, Bolao $bolao): bool
    {
        return $user === $bolao->getUsuario();
    }

    private function canList(Usuario $user, Bolao $bolao): bool
    {
        return $user === $bolao->getUsuario();
    }

    private function canDownload(Usuario $user, Bolao $bolao): bool
    {
        return $user === $bolao->getUsuario();
    }

    private function canDelete(Usuario $user, Bolao $bolao): bool
    {
        return $user === $bolao->getUsuario();
    }
}
