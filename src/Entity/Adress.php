<?php

namespace App\Entity;

use App\Repository\AdressRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdressRepository::class)
 */
class Adress
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adressepostale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $complementadresse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $region;

    /**
     * @ORM\Column(type="integer")
     */
    private $codepostale;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="adress", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $adresseuser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getAdressepostale(): ?string
    {
        return $this->adressepostale;
    }

    public function setAdressepostale(string $adressepostale): self
    {
        $this->adressepostale = $adressepostale;

        return $this;
    }

    public function getComplementadresse(): ?string
    {
        return $this->complementadresse;
    }

    public function setComplementadresse(?string $complementadresse): self
    {
        $this->complementadresse = $complementadresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCodepostale(): ?int
    {
        return $this->codepostale;
    }

    public function setCodepostale(int $codepostale): self
    {
        $this->codepostale = $codepostale;

        return $this;
    }

    public function getAdresseuser(): ?User
    {
        return $this->adresseuser;
    }

    public function setAdresseuser(User $adresseuser): self
    {
        $this->adresseuser = $adresseuser;

        return $this;
    }
}
