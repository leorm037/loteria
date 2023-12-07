<?php

namespace App\Entity;

use App\Repository\BolaoRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BolaoRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache]
class Bolao extends AbstractEntity {

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(message: 'É obrigatório informar o nome do bolão.')]
    private ?string $nome = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comprovanteAposta = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'É obrigatório informar o concurso.')]
    private ?Concurso $concurso = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'É obrigatório informar o dono do bolão.')]
    private ?Usuario $usuario = null;

    #[ORM\Column]
    protected ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $updatedAt = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getNome(): ?string {
        return $this->nome;
    }

    public function setNome(string $nome): static {
        $this->nome = $nome;

        return $this;
    }

    public function getComprovanteAposta(): ?string {
        return $this->comprovanteAposta;
    }

    public function setComprovanteAposta(?string $comprovanteAposta): static {
        $this->comprovanteAposta = $comprovanteAposta;

        return $this;
    }

    public function getConcurso(): ?Concurso {
        return $this->concurso;
    }

    public function setConcurso(?Concurso $concurso): static {
        $this->concurso = $concurso;

        return $this;
    }

    public function getUsuario(): ?Usuario {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static {
        $this->usuario = $usuario;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): static {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
