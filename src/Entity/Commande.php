<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    const DEVISE = 'eur';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandeurMenu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commandeur;

    /**
     * @ORM\OneToMany(targetEntity=Food::class, mappedBy="menucommandeur")
     */
    private $menu;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datecommande;

    /**
     * @ORM\Column(type="float")
     */
    private $prixtotal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modepaiement;

    /**
     * @ORM\Column(type="integer")
     */
    private $etatpaiement;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datelivraison;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrmenu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lieulivraison;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;





    //Créer la date automatiquement pour la creation de commande
    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->menu = new ArrayCollection();
    }





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeur(): ?User
    {
        return $this->commandeur;
    }

    public function setCommandeur(?User $commandeur): self
    {
        $this->commandeur = $commandeur;

        return $this;
    }

    /**
     * @return Collection|Food[]
     */
    public function getMenu(): Collection
    {
        return $this->menu;
    }

    public function addMenu(Food $menu): self
    {
        if (!$this->menu->contains($menu)) {
            $this->menu[] = $menu;
            $menu->setMenucommandeur($this);
        }

        return $this;
    }

    public function removeMenu(Food $menu): self
    {
        if ($this->menu->removeElement($menu)) {
            // set the owning side to null (unless already changed)
            if ($menu->getMenucommandeur() === $this) {
                $menu->setMenucommandeur(null);
            }
        }

        return $this;
    }

    public function getDatecommande(): ?\DateTimeInterface
    {
        return $this->datecommande;
    }

    public function setDatecommande(\DateTimeInterface $datecommande): self
    {
        $this->datecommande = $datecommande;

        return $this;
    }

    public function getPrixtotal(): ?float
    {
        return $this->prixtotal;
    }

    public function setPrixtotal(float $prixtotal): self
    {
        $this->prixtotal = $prixtotal;

        return $this;
    }

    public function getModepaiement(): ?string
    {
        return $this->modepaiement;
    }

    public function setModepaiement(string $modepaiement): self
    {
        $this->modepaiement = $modepaiement;

        return $this;
    }

    public function getEtatpaiement(): ?int
    {
        return $this->etatpaiement;
    }

    public function setEtatpaiement(int $etatpaiement): self
    {
        $this->etatpaiement = $etatpaiement;

        return $this;
    }

    public function getDatelivraison(): ?\DateTimeInterface
    {
        return $this->datelivraison;
    }

    public function setDatelivraison(\DateTimeInterface $datelivraison): self
    {
        $this->datelivraison = $datelivraison;

        return $this;
    }

    public function getNbrmenu(): ?int
    {
        return $this->nbrmenu;
    }

    public function setNbrmenu(int $nbrmenu): self
    {
        $this->nbrmenu = $nbrmenu;

        return $this;
    }

    public function getLieulivraison(): ?string
    {
        return $this->lieulivraison;
    }

    public function setLieulivraison(string $lieulivraison): self
    {
        $this->lieulivraison = $lieulivraison;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
