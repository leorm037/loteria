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

use App\Entity\Concurso;
use App\Helper\DateTimeHelper;
use App\Repository\ConcursoRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConcursoBuilder
{

    public static function getConcurso1234MegaSenaDezenas1a6Db(EntityManagerInterface $entityManager): Concurso
    {
        $loteriaDb = LoteriaBuilder::getLoteriaMegaSenaDb($entityManager);

        $concurso = (new Concurso())
                ->setLoteria($loteriaDb)
                ->setLocal('Casa da Sorte')
                ->setMunicipio('São Paulo')
                ->setUf('SP')
                ->setDezena(range(1, 6, 1))
                ->setApuracaoData(DateTimeHelper::stringToDateTime('1/2/2023', 'd/m/Y'))
                ->setNumero(1234)
        ;

        /** @var ConcursoRepository $concursoRepository */
        $concursoRepository = $entityManager->getRepository(Concurso::class);

        $concursoDb = $concursoRepository->findByLoteriaAndNumero($loteriaDb, $concurso->getNumero());

        if (null !== $concursoDb) {
            $concursoDb
                    ->setLocal($concurso->getLocal())
                    ->setMunicipio($concurso->getMunicipio())
                    ->setUf($concurso->getUf())
                    ->setDezena($concurso->getDezena())
                    ->setApuracaoData($concurso->getApuracaoData())
            ;

            $concursoRepository->save($concursoDb, true);

            return $concursoDb;
        }

        $concursoRepository->save($concurso, true);

        return $concurso;
    }
}
