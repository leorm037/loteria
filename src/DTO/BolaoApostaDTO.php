<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\DTO;

class BolaoApostaDTO
{
    private string $idLoteria;
    private int $dezenasMarcar;
    private array $dezenas;

    public function getIdLoteria(): string
    {
        return $this->idLoteria;
    }

    public function setIdLoteria(string $idLoteria): static
    {
        $this->idLoteria = $idLoteria;

        return $this;
    }

    public function getDezenasMarcar(): int
    {
        return $this->dezenasMarcar;
    }

    public function getDezenas(): array
    {
        return $this->dezenas;
    }

    public function setDezenasMarcar(int $dezenasMarcar): static
    {
        $this->dezenasMarcar = $dezenasMarcar;

        return $this;
    }

    public function setDezenas(array $dezenas): static
    {
        $this->dezenas = $dezenas;

        return $this;
    }
}
