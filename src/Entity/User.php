<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    denormalizationContext: ['groups' => 'user:write'],
    normalizationContext: ['groups' => 'user:read']

)]
#[UniqueEntity(['fields' => 'username'])]
#[UniqueEntity(['fields' => 'email'])]
#[ApiFilter(PropertyFilter::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['user:write', 'user:read'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[Groups(['user:write'])]
    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['user:write', 'user:read', 'cheese_listening:item:get', 'cheese_listening:write'])]
    #[Assert\NotBlank]
    private $username;

    //orphanRemoval -> kasowanie potomkow
    //cascade : dodawanie do bazy przy relacjach
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: CheeseListening::class, cascade: ['persist'], orphanRemoval: true )]
    //tutaj tylko IRI mozna przekazac, przy write
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Valid]
    //mozna odnosic sie pod roznych zrodel, przydatne w relacji
    #[ApiSubresource]
    private $cheeseListenings;

    public function __construct()
    {
        $this->cheeseListenings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
        return (string)$this->email;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|CheeseListening[]
     */
    public function getCheeseListenings(): Collection
    {
        return $this->cheeseListenings;
    }

    public function addCheeseListening(CheeseListening $cheeseListening): self
    {
        if (!$this->cheeseListenings->contains($cheeseListening)) {
            $this->cheeseListenings[] = $cheeseListening;
            $cheeseListening->setOwner($this);
        }

        return $this;
    }

    public function removeCheeseListening(CheeseListening $cheeseListening): self
    {
        if ($this->cheeseListenings->removeElement($cheeseListening)) {
            // set the owning side to null (unless already changed)
            if ($cheeseListening->getOwner() === $this) {
                $cheeseListening->setOwner(null);
            }
        }

        return $this;
    }
}
