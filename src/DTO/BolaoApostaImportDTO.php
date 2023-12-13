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

use App\Entity\Bolao;

class BolaoApostaImportDTO
{
    private string $fileCsv;
    private Bolao $bolao;

    public function getBolao(): Bolao
    {
        return $this->bolao;
    }

    public function setBolao(Bolao $bolao): static
    {
        $this->bolao = $bolao;

        return $this;
    }

    public function getFileCsv(): string
    {
        return $this->fileCsv;
    }

    public function setFileCsv(string $fileCsv): static
    {
        $this->fileCsv = $fileCsv;

        return $this;
    }
}
