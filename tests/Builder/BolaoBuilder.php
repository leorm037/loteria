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

use App\Entity\Bolao;
use App\Entity\Usuario;
use App\Repository\BolaoRepository;
use Doctrine\ORM\EntityManagerInterface;

class BolaoBuilder
{

    public static function getBolaoDaAdminMariaDaMegaSenaConcurso1234Dezenas1a6Db(EntityManagerInterface $entityManager): Bolao
    {
        $adminMariaDb = UsuarioBuilder::getAdminMaria();

        return self::getBolaoDaMegaSenaConcurso1234Dezenas1a6Db($entityManager, $adminMariaDb);
    }

    public static function getBolaoDaUsuarioJoaoDaMegaSenaConcurso1234Dezenas1a6Db(EntityManagerInterface $entityManager): Bolao
    {
        $usuarioJoaoDb = UsuarioBuilder::getUsuarioJoaoDb($entityManager);

        return self::getBolaoDaMegaSenaConcurso1234Dezenas1a6Db($entityManager, $usuarioJoaoDb);
    }

    public static function getBolaoDaMegaSenaConcurso1234Dezenas1a6Db(EntityManagerInterface $entityManager, Usuario $usuarioDb): Bolao
    {
        $concursoDb = ConcursoBuilder::getConcurso1234MegaSenaDezenas1a6Db($entityManager);

        $bolao = (new Bolao())
                ->setNome('Bolao da semana - ' . $usuarioDb->getNome())
                ->setValorCota(10)
                ->setConcurso($concursoDb)
                ->setUsuario($usuarioDb)
        ;

        /** @var BolaoRepository $bolaoRepository */
        $bolaoRepository = $entityManager->getRepository(Bolao::class);

        $bolaoDb = $bolaoRepository->findOneBy(['nome' => $bolao->getNome()]);

        if (null !== $bolaoDb) {
            $bolaoDb
                    ->setNome($bolao->getNome())
                    ->setValorCota($bolao->getValorCota())
                    ->setConcurso($concursoDb)
                    ->setUsuario($usuarioDb)
            ;

            $bolaoRepository->save($bolaoDb, true);

            return $bolaoDb;
        }

        $bolaoRepository->save($bolao, true);

        return $bolao;
    }
}
