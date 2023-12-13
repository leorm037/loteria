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

use App\Entity\Loteria;
use App\Entity\Usuario;

class BolaoDTO
{
    private Loteria $loteria;
    private int $concursoNumero;
    private string $nome;
    private string $valorCota = '0';
    private Usuario $usuario;
    private ?string $comprovante = null;

    public function getComprovante(): ?string
    {
        return $this->comprovante;
    }

    public function setComprovante(?string $comprovante): static
    {
        $this->comprovante = $comprovante;

        return $this;
    }

    public function getValorCota(): ?string
    {
        return $this->valorCota;
    }

    public function setValorCota(?string $valorCota): static
    {
        $this->valorCota = $valorCota;

        return $this;
    }

    public function getConcursoNumero(): int
    {
        return $this->concursoNumero;
    }

    public function setConcursoNumero(int $concursoNumero): void
    {
        $this->concursoNumero = $concursoNumero;
    }

    public function getLoteria(): Loteria
    {
        return $this->loteria;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setLoteria(Loteria $loteria): static
    {
        $this->loteria = $loteria;

        return $this;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function setUsuario(Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }
}
