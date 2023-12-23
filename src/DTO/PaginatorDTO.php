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

use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorDTO
{
    private int $page = 1;
    private int $maxResult = 10;
    private ?Paginator $result;

    public function getResult(): ?Paginator
    {
        return $this->result;
    }

    public function getCount(): int
    {
        if (null === $this->result) {
            return 0;
        }

        return $this->result->count();
    }

    public function setResult(?Paginator $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getPages(): int
    {
        $mod = $this->getCount() % $this->getMaxResult();
        $pages = floor($this->getCount() / $this->getMaxResult());

        if ($mod > 0) {
            return $pages + 1;
        }

        return $pages;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getFirstResult(): int
    {
        return ($this->getPage() - 1) * $this->getMaxResult();
    }

    public function getMaxResult(): int
    {
        return $this->maxResult;
    }

    public function setPage(int $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function setMaxResult(int $maxResult): static
    {
        $this->maxResult = $maxResult;

        return $this;
    }
}
