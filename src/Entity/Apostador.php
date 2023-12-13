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

use App\Repository\ApostadorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ApostadorRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class Apostador extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'É obrigatório informar o bolão.')]
    private ?Bolao $bolao = null;

    #[ORM\ManyToOne]
    private ?Usuario $usuario = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isPago = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comprovante = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(message: 'É obrigatório informar o nome do apostador.')]
    private ?string $nome = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $celular = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(options: ['default' => 1])]
    private ?int $quantidadeCota = 1;

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function isIsPago(): ?bool
    {
        return $this->isPago;
    }

    public function setIsPago(bool $isPago): static
    {
        $this->isPago = $isPago;

        return $this;
    }

    public function getComprovante(): ?string
    {
        return $this->comprovante;
    }

    public function setComprovante(?string $comprovante): static
    {
        $this->comprovante = $comprovante;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular(?string $celular): static
    {
        $this->celular = $celular;

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

    public function getQuantidadeCota(): ?int
    {
        return $this->quantidadeCota;
    }

    public function setQuantidadeCota(int $quantidadeCota): static
    {
        $this->quantidadeCota = $quantidadeCota;

        return $this;
    }
}
