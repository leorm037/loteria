<?php

namespace App\DataFixtures;

use App\Entity\Loteria;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LoteriaFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $loterias = [
            [
                'nome' => 'Mega-Sena',
                'api' => 'https://servicebus2.caixa.gov.br/portaldeloterias/api/megasena',
                'marcar' => range(6, 15, 1),
                'premiar' => range(4, 6, 1),
                'dezena' => range(1, 60, 1),
                'logo' => 'logo-mega-sena-600x200.png',
            ]
        ];

        foreach ($loterias as $item) {
            $loteria = new Loteria();

            $loteria->setNome($item['nome'])
                    ->setApi($item['api'])
                    ->setMarcar($item['marcar'])
                    ->setPremiar($item['premiar'])
                    ->setDezena($item['dezena'])
                    ->setLogo($item['logo'])
            ;

            $manager->persist($loteria);
        }

        $manager->flush();
    }
}
