<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"email"}, message="Ce mail existe déjà")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Mettez un email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mdphashed;

    /**
     * Confiramtion de MDP
     * @Assert\EqualTo(propertyPath="mdphashed", message="Votre mot de passe ne sont pas identiques")
     */
    public $passwordConfirm;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, minMessage="Votre introduction doit avoir au moins 5 caractères")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     *  @Assert\Length(min=5,minMessage="Votre description doit avoir au moins 5 caractères")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Food::class, mappedBy="author", orphanRemoval=true)
     */
    private $food;



    //Concater le nom et prénom 
    public function getFullname()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * On va initialiser un Slug à partir de (firstname etlastname) avant toute persistance
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializuSlug()
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstname . ' ' . $this->lastname);
        }
    }




    public function __construct()
    {
        $this->food = new ArrayCollection();
    }





    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getMdphashed(): ?string
    {
        return $this->mdphashed;
    }

    public function setMdphashed(string $mdphashed): self
    {
        $this->mdphashed = $mdphashed;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Food[]
     */
    public function getFood(): Collection
    {
        return $this->food;
    }

    public function addFood(Food $food): self
    {
        if (!$this->food->contains($food)) {
            $this->food[] = $food;
            $food->setAuthor($this);
        }

        return $this;
    }

    public function removeFood(Food $food): self
    {
        if ($this->food->removeElement($food)) {
            // set the owning side to null (unless already changed)
            if ($food->getAuthor() === $this) {
                $food->setAuthor(null);
            }
        }

        return $this;
    }





    /**
     * @see UserInterface
     */
    public function getRoles()
    {

        //On va récupérer tous les titres rôles qui se trouve dans tableau userRoles()
    //    $roles = $this->userRoles->map(function ($role) {
    //        return $role->getTitle();
    //    })->toArray();

        //On va rajouter dans ce nouveau tableau le rôle ROLE_USER
        $roles[] = 'ROLE_USER';

        //On  retourne le tableau qui contient tous les rôles 
        return $roles;
    }

    /**
     * This method is deprecated since Symfony 5.3, implement it from {@link PasswordAuthenticatedUserInterface} instead.
     * @return string|null The hashed password if any
     */
    public function getPassword(): ?string
    {
        return $this->mdphashed;
    }

    /**
     * @see UserInterface
     * This method is deprecated since Symfony 5.3, implement it from {@link LegacyPasswordAuthenticatedUserInterface} instead.
     * @return string|null The salt
     */
    public function getSalt()
    {
        //Notre algorythme utilise déjà le Salt (config/packages/security.yaml)
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        //
    }


    /**
     * @see UserInterface
     *
     * @return string
     * @deprecated since Symfony 5.3, use getUserIdentifier() instead
     */
    public function getUsername()
    {
        return $this->email;
    }

}
