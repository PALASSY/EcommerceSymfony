<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PrePersist;
use App\Repository\ReservationRepository;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Table::class, inversedBy="tables")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tabledisponible;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("+1 hours", message="Aucune table disponible, chercher un autre créneau horaire", groups="front")
     * @Assert\Type(type="\DateTimeInterface", message="Le format doit être une date")
     * @Assert\NotNull
     */
    private $date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaire;



    //Créer la date automatiquement pour la table
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }




    //On va faire une comparaison entre (les dates et heures prises) et (les dates et heures souhaitées) de reservation 
    public function isBookabledays()
    {
        //Les dates prises
        $notAvailableDays = $this->tabledisponible->getNotAvailableDays();

        //Les dates souhaitées
        $bookingDays = $this->getDays();

        //Convertir en date pour faciliter la comparaison
        $notDays = array_map(function ($day) {
            return $day->format('Y-m-d H:m:s');
        }, $notAvailableDays);

        $days = array_map(function ($day) {
            return $day->format('Y-m-d H:m:s');
        }, $bookingDays);

        //Comparaison (array_search) — Recherche dans un tableau la première clé associée à la valeur
        //Si la date prise === la date souhaitée (false, on ne peut pas réserver à cette date)
        foreach ($days as $day) {
            if (array_search($day, $notDays) !== false)
                return false;
        }
        //Si date prise !== la date souhaitée (true, on peut reserver cette date)
        return true;
    }





    //On va récupérer toutes les dates(et heures) que le client souhaite réserver (càd en ce moment)
    public function getDays()
    {
        //Récupérer toutes les dates(et heures) dans l'Entity Reservation (convertit en seconde depuis 1-jan-70)
        $resultat = [$this->getDate()->getTimestamp()];

        //Reconvertir en date, array_map() permet d'appliquer une function sur les éléménts d'un tableau
        $days = array_map(function ($dayTimestamp) {
            return new \DateTime(date(
                'Y-m-d H:i:s',
                $dayTimestamp
            ));
        }, $resultat);

        //Retourner le tableau
        return $days;
    }






    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getTabledisponible(): ?Table
    {
        return $this->tabledisponible;
    }

    public function setTabledisponible(?Table $tabledisponible): self
    {
        $this->tabledisponible = $tabledisponible;

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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
