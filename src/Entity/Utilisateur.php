<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\MeController;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Erreur : Il existe déja un compte utilisateur avec cet email')]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:User'])]
    #[Assert\IsNull()]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(["read", "write"])]
    #[Assert\Email(message:'Email invalide')]
    private $email;

    #[ORM\Column(type: 'json')]
    //#[Assert\IsNull()]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank()]
    #[Assert\Length([
        'min' => 8,
        'minMessage' => 'Votre mot de passe de contenir au moins {{ limit }} caractères',
    ])]
    #[Groups(["write"])]
    private $password;

    #[Assert\Length([
        'min' => 2,
        'max' => 300,
        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères',
        'maxMessage' => 'Votre nom ne peut depasser {{ limit }} caractères',
    ])]
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    private $nom;
    
    #[Assert\Length([
        'min' => 2,
        'max' => 300,
        'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères',
        'maxMessage' => 'Votre prénom ne peut depasser {{ limit }} caractères',
    ])]
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    private $prenom;

    #[ORM\Column(type: 'string')]
    #[Assert\Length([
        'min' => 9,
        'max' => 11,
        'minMessage' => 'Votre numéro doit contenir au moins {{ limit }} caractères',
        'maxMessage' => 'Votre numéro ne peut depasser {{ limit }} caractères',
    ])]
    #[Groups(["read", "write"])]
    private $telephone;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Country(message:'Nom de pays invalide')]
    #[Groups(["read", "write"])]
    private $paysDeResidence;

    #[ORM\Column(type: 'datetime_immutable', options : ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(["read"])]
    private $dateDeCreation;

    #[ORM\OneToOne(mappedBy: 'proprietaire', targetEntity: Panier::class, cascade: ['all'])]
    #[Groups(["read"])]
    private $panier;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: ApiToken::class, orphanRemoval: true)]
    private $apiTokens;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    public function __construct(){
        $this->dateDeCreation = new \DateTimeImmutable();
        $this->apiTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id) : self
    {
        $this->id = $id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPaysDeResidence(): ?string
    {
        return $this->paysDeResidence;
    }

    public function setPaysDeResidence(string $paysDeResidence): self
    {
        $this->paysDeResidence = $paysDeResidence;

        return $this;
    }
    
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
    

    public function getDateDeCreation(): ?\DateTimeImmutable
    {
        return $this->dateDeCreation;
    }

    public function setDateDeCreation(\DateTimeImmutable $dateDeCreation): self
    {
        $this->dateDeCreation = $dateDeCreation;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(Panier $panier): self
    {
        // set the owning side of the relation if necessary
        if ($panier->getProprietaire() !== $this) {
            $panier->setProprietaire($this);
        }

        $this->panier = $panier;

        return $this;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens[] = $apiToken;
            $apiToken->setUtilisateur($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getUtilisateur() === $this) {
                $apiToken->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }
    //Cette methode permet de recuperer les données de l'utilisateur à partir du token
    public static function createFromPayload($id, array $payload)
    {
        
        return (new Utilisateur())->setId($id)->setEmail($payload['email'] ?? '');
    }
}
