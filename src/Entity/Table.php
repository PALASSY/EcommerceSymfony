<?php

namespace App\Entity;

use App\Repository\TableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TableRepository::class)
 * @ORM\Table(name="`table`")
 */
class Table
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
    private $place;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tables")
     * @ORM\JoinColumn(nullable=false)
     */
    private $authortable;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="tabledisponible", orphanRemoval=true)
     */
    private $tables;




    //Créer la date automatiquement pour la table
    public function __construct()
    {
        $this->created_at = new \DateTime();
        //$this->tablereservations = new ArrayCollection();
        $this->tables = new ArrayCollection();
    }



    //Récupérer toutes les dates(et heures) prises(par le client) dans l'entity Reservation()
    public function getNotAvailableDays()
    {
        //Mettre dans un tableau
        $notAvailableDays = [];

        //Récupérer toutes les dates(et heures) prises dans l'Entity réservation 
        foreach ($this->tables as $datereservee) {

            // on va créer un tableau contenant toutes les dates de réservation (convertit en seconde depuis 1-jan-70)
            $resultat = [$datereservee->getDate()->getTimeStamp()];

            //Reconvertir en date, array_map() permet d'appliquer une function sur les éléménts d'un tableau
            $days = array_map(function ($dayTimestamp) {
                return new \DateTime(date('Y-m-d H:i:s', $dayTimestamp));
            }, $resultat);

            //Fusionner les 2 tableaux avec array_merge()
            $notAvailableDays = array_merge($notAvailableDays, $days);
        }

        //On retourne le tableau
        return $notAvailableDays;
    }





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getAuthortable(): ?User
    {
        return $this->authortable;
    }

    public function setAuthortable(?User $authortable): self
    {
        $this->authortable = $authortable;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Reservation $table): self
    {
        if (!$this->tables->contains($table)) {
            $this->tables[] = $table;
            $table->setTabledisponible($this);
        }

        return $this;
    }

    public function removeTable(Reservation $table): self
    {
        if ($this->tables->removeElement($table)) {
            // set the owning side to null (unless already changed)
            if ($table->getTabledisponible() === $this) {
                $table->setTabledisponible(null);
            }
        }

        return $this;
    }
}
