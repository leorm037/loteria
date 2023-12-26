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

use App\Repository\ApostaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ApostaRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class Aposta extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'É obrigatório informar o concurso.')]
    private ?Concurso $concurso = null;

    #[ORM\ManyToOne]
    private ?Usuario $usuario = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'É obrigatório informar as dezenas.')]
    private array $dezena = [];

    #[ORM\ManyToOne]
    private ?Bolao $bolao = null;

    #[ORM\Column]
    private ?bool $isConferido = false;

    #[ORM\Column(nullable: true)]
    private ?int $acerto = null;

    #[ORM\Column]
    protected ?bool $isPremiado = false;

    #[ORM\Column]
    protected ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getConcurso(): ?Concurso
    {
        return $this->concurso;
    }

    public function setConcurso(?Concurso $concurso): static
    {
        $this->concurso = $concurso;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getDezena(): array
    {
        sort($this->dezena);

        return $this->dezena;
    }

    public function setDezena(array $dezena): static
    {
        $this->dezena = $dezena;

        return $this;
    }

    public function getBolao(): ?Bolao
    {
        return $this->bolao;
    }

    public function setBolao(?Bolao $bolao): static
    {
        $this->bolao = $bolao;

        return $this;
    }

    public function isIsConferido(): ?bool
    {
        return $this->isConferido;
    }

    public function setIsConferido(bool $isConferido): static
    {
        $this->isConferido = $isConferido;

        return $this;
    }

    public function getAcerto(): ?int
    {
        return $this->acerto;
    }

    public function setAcerto(?int $acerto): static
    {
        $this->acerto = $acerto;

        return $this;
    }

    public function isPremiado(): ?bool
    {
        return $this->isPremiado;
    }

    public function setPremiado(bool $isPremiado): static
    {
        $this->isPremiado = $isPremiado;

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
}
