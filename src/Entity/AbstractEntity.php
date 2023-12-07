<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Helper\DateTimeHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @property mixed $createdAt
 * @property mixed $updatedAt
 */
abstract class AbstractEntity
{
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt();
        $this->slugger();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt();
        $this->slugger();
    }

    private function createdAt(): void
    {
        if (property_exists(static::class, 'createdAt') && null === $this->createdAt) {
            $this->createdAt = DateTimeHelper::currentDateTimeImmutable();
        }
    }

    private function updatedAt(): void
    {
        if (property_exists(static::class, 'updatedAt')) {
            $this->updatedAt = DateTimeHelper::currentDateTime();
        }
    }

    private function slugger(): void
    {
        if (property_exists(static::class, 'slug')) {
            $slugger = new AsciiSlugger();
            $this->slug = strtolower($slugger->slug($this->getNome()));
        }
    }
}
