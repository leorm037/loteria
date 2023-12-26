<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Tests\Builder;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;

class UsuarioBuilder
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public static function getUsuarioJoao(): Usuario
    {
        return (new Usuario())
                        ->setNome('João Maria José Oliveira Neves Fraga')
                        ->setCelular('+5561998877665')
                        ->setEmail('joao@teste.com.br')
                        ->setIsVerified(true)
                        ->setRoles([self::ROLE_USER])
                        ->setPassword('$2y$13$lRrYQqiDOmbNwfjMRjSf9OL0U4TTyg.rrO0j118NW37yG9jgEeR3.') // 123456
        ;
    }

    public static function getUsuarioJoaoDb(EntityManagerInterface $entityManager): Usuario
    {
        $usuario = self::getUsuarioJoao();

        $usuarioRepository = $entityManager->getRepository(Usuario::class);

        $usuarioDb = $usuarioRepository->findOneBy(['email' => $usuario->getEmail()]);

        if (null !== $usuarioDb) {
            $usuarioDb
                    ->setNome($usuario->getNome())
                    ->setEmail($usuario->getEmail())
                    ->setCelular($usuario->getCelular())
                    ->setPassword($usuario->getPassword())
                    ->setRoles($usuario->getRoles())
                    ->setIsVerified($usuario->isVerified())
            ;

            $usuarioRepository->save($usuarioDb, true);

            return $usuarioDb;
        }

        $usuarioRepository->save($usuario, true);

        return $usuario;
    }

    public static function getAdminMaria(): Usuario
    {
        return (new Usuario())
                        ->setNome('Maria Pereira Pires Silva Santos')
                        ->setCelular('+5561977733123')
                        ->setEmail('maria@teste.com.br')
                        ->setIsVerified(true)
                        ->setRoles([self::ROLE_ADMIN])
                        ->setPassword('$2y$13$lRrYQqiDOmbNwfjMRjSf9OL0U4TTyg.rrO0j118NW37yG9jgEeR3.') // 123456
        ;
    }

    public static function getAdminMariaDb(EntityManagerInterface $entityManager): Usuario
    {
        $admin = self::getAdminMaria();

        $usuarioRepository = $entityManager->getRepository(Usuario::class);

        $adminDb = $usuarioRepository->findOneBy(['email' => $admin->getEmail()]);

        if (null !== $adminDb) {
            $adminDb
                    ->setNome($admin->getNome())
                    ->setEmail($admin->getEmail())
                    ->setCelular($admin->getCelular())
                    ->setPassword($admin->getPassword())
                    ->setRoles($admin->getRoles())
                    ->setIsVerified($admin->isVerified())
            ;

            $usuarioRepository->save($adminDb, true);

            return $adminDb;
        }

        $usuarioRepository->save($admin, true);

        return $admin;
    }
}
