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

use App\Repository\ConcursoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ConcursoRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class Concurso extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $apuracaoData = null;

    #[ORM\Column(nullable: true)]
    private ?array $rateioPremio = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $local = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $municipio = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $uf = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Cache]
    private ?Loteria $loteria = null;

    #[ORM\Column(nullable: true)]
    private ?array $dezena = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getApuracaoData(): ?\DateTimeInterface
    {
        return $this->apuracaoData;
    }

    public function setApuracaoData(?\DateTimeInterface $apuracaoData): static
    {
        $this->apuracaoData = $apuracaoData;

        return $this;
    }

    public function getRateioPremio(): ?array
    {
        return $this->rateioPremio;
    }

    public function setRateioPremio(?array $rateioPremio): static
    {
        $this->rateioPremio = $rateioPremio;

        return $this;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(?string $local): static
    {
        $this->local = $local;

        return $this;
    }

    public function getMunicipio(): ?string
    {
        return $this->municipio;
    }

    public function setMunicipio(?string $municipio): static
    {
        $this->municipio = $municipio;

        return $this;
    }

    public function getUf(): ?string
    {
        return $this->uf;
    }

    public function setUf(?string $uf): static
    {
        $this->uf = $uf;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLoteria(): ?Loteria
    {
        return $this->loteria;
    }

    public function setLoteria(?Loteria $loteria): static
    {
        $this->loteria = $loteria;

        return $this;
    }

    public function getDezena(): ?array
    {
        return $this->dezena;
    }

    public function setDezena(?array $dezena): static
    {
        $this->dezena = $dezena;

        return $this;
    }
}
