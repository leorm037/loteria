<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Message;

use App\Entity\Bolao;

final class BolaoConferidoMessage
{
    private Bolao $bolao;

    public function __construct(Bolao $bolao)
    {
        $this->bolao = $bolao;
    }

    public function getBolao(): Bolao
    {
        return $this->bolao;
    }
}
