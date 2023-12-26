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

use App\Entity\Loteria;
use App\Repository\LoteriaRepository;
use Doctrine\ORM\EntityManagerInterface;

class LoteriaBuilder
{

    public static function getLoteriaMegaSenaDb(EntityManagerInterface $entityManager): Loteria
    {
        $loteria = (new Loteria())
                ->setNome('Mega-Sena')
                ->setApi('https://servicebus2.caixa.gov.br/portaldeloterias/api/megasena')
                ->setMarcar(range(6, 15, 1))
                ->setPremiar(range(4, 6, 1))
                ->setDezena(range(1, 60, 1))
                ->setLogo('logo-mega-sena-600x200.png')
        ;

        /** @var LoteriaRepository $loteriaRepository */
        $loteriaRepository = $entityManager->getRepository(Loteria::class);

        $loteriaDb = $loteriaRepository->findOneBy(['nome' => $loteria->getNome()]);

        if (null !== $loteriaDb) {
            $loteriaDb
                    ->setNome($loteria->getNome())
                    ->setApi($loteria->getApi())
                    ->setMarcar($loteria->getMarcar())
                    ->setPremiar($loteria->getPremiar())
                    ->setDezena($loteria->getDezena())
                    ->setLogo($loteria->getLogo())
            ;

            $loteriaRepository->save($loteriaDb, true);
            
            return $loteriaDb;
        }

        $loteriaRepository->save($loteria, true);

        return $loteria;
    }

    public static function getLoteriaLotofacilDb(EntityManagerInterface $entityManager): Loteria
    {
        $loteria = (new Loteria())
                ->setNome('Lotofácil')
                ->setApi('https://servicebus2.caixa.gov.br/portaldeloterias/api/lotofacil')
                ->setMarcar(range(15, 20, 1))
                ->setPremiar(range(11, 15, 1))
                ->setDezena(range(1, 25, 1))
                ->setLogo('logo-default-600x200.png')

        ;

        $loteriaRepository = $entityManager->getRepository(Loteria::class);

        $loteriaRepository->save($loteria, true);

        return $loteria;
    }
}
