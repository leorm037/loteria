<?php

namespace App\Factory;

use App\Entity\Concurso;
use App\Entity\Loteria;
use App\Helper\DateTimeHelper;

class ConcursoFactory
{

    public static function buildFromJson(Loteria $loteria, string $json): Concurso
    {
        $concurso = new Concurso();

        $objJson = json_decode($json);

        $apuracaoData = DateTimeHelper::stringToDateTimeImmutable(
                        $objJson->dataApuracao,
                        'd/m/Y',
                        'America/Sao_Paulo'
        );

        $municipioUf = explode(',', $objJson->nomeMunicipioUFSorteio);
        $municipio = trim($municipioUf[0]);
        $uf = trim($municipioUf[1]);

        return $concurso
                        ->setUf($uf)
                        ->setLoteria($loteria)
                        ->setMunicipio($municipio)
                        ->setNumero($objJson->numero)
                        ->setApuracaoData($apuracaoData)
                        ->setLocal($objJson->localSorteio)
                        ->setDezena($objJson->listaDezenas)
                        ->setRateioPremio($objJson->listaRateioPremio)
        ;
    }

    public static function updateFromJson(Concurso &$concurso, Concurso $sorteio): void
    {
        $concurso
                ->setDezena($sorteio->getDezena())
                ->setApuracaoData($sorteio->getApuracaoData())
                ->setLocal($sorteio->getLocal())
                ->setMunicipio($sorteio->getMunicipio())
                ->setUf($sorteio->getUf())
                ->setRateioPremio($sorteio->getRateioPremio())
        ;
    }
}
