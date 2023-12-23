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

class BolaoApostadorPesquisarDTO
{
    public const PAGO_YES = 'yes';
    public const PAGO_NO = 'no';

    private ?string $nome = null;
    private ?string $pago = null;

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function getPago(): ?string
    {
        return $this->pago;
    }

    public function isPago(): ?bool
    {
        switch ($this->pago) {
            case self::PAGO_YES:
                return true;
            case self::PAGO_NO:
                return false;
            default:
                return null;
        }
    }

    public function setNome(?string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function setPago(?string $pago): static
    {
        $this->pago = $pago;

        return $this;
    }
}
