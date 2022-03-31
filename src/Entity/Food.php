<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FoodRepository;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;


use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=FoodRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields = {"menu"}, message="Ce menu existe déjà")
 */
class Food
{

    //validation contrainte (IsTrue)
    protected $token;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=2,max=255,minMessage="votre titre du menu doit faire au moins 2 caractères", 
     * maxMessage="Votre titre du menu ne doit pas dépasser 255 caractères")
     */
    private $menu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datecreationmenu;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="food", orphanRemoval=true)
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="food")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="menu")
     */
    private $menucommandeur;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="food", orphanRemoval=true)
     */
    private $comments;



    public function __construct() 
    {
        $this->datecreationmenu = new \DateTime();
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    /**
     * LifeCycle Callbacks slug(menu)
     *  @ORM\PrePersist
     *  @ORM\PreUpdate
     */
    public function initializeSlug()
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->menu);
        }
    }

    /**
     * Récupérer un commentaire de l'user lamba par rapport à l'annonce(Food())
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromauthor(User $author)
    {
        //Récupérer tous les commentaires
        foreach ($this->comments as  $comment) {
            //Si l'utilisateur lamba est l'author de commentaire on récupére le commentaire 
            if ($author == $comment->getAuthor()) {
                return $comment;
            }
            return null;
        }
    }



    public function getAvarageRatings()
    {
        //Récupérer le nombre total des commentaires dans un tableau
        //Faire une itération(fait répéter) à partir d'un nombre de départ(0) pour rajouter les notes de chaque commentaire dans un tableau
        $sum = array_reduce($this->comments->toArray(),function ($total,$comment)
        {
            return $total + $comment->getRating();
        },0);

        //Calcul de ces notes commentaires
        //Vérifier s'il y a au moins un commentaire 
        if (count($this->comments) > 0) {
            return $sum / count($this->comments);
        }else{
            return 0;
        }
    }






    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenu(): ?string
    {
        return $this->menu;
    }

    public function setMenu(string $menu): self
    {
        $this->menu = $menu;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getDatecreationmenu(): ?\DateTimeInterface
    {
        return $this->datecreationmenu;
    }

    public function setDatecreationmenu(\DateTimeInterface $datecreationmenu): self
    {
        $this->datecreationmenu = $datecreationmenu;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setFood($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getFood() === $this) {
                $image->setFood(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getMenucommandeur(): ?Commande
    {
        return $this->menucommandeur;
    }

    public function setMenucommandeur(?Commande $menucommandeur): self
    {
        $this->menucommandeur = $menucommandeur;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setFood($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFood() === $this) {
                $comment->setFood(null);
            }
        }

        return $this;
    }
}
