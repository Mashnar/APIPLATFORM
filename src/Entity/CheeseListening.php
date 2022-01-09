<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\CheeseListeningRepository;
use Carbon\Carbon;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: CheeseListeningRepository::class)]
#[ApiResource
(
    collectionOperations: ['get', 'post'],
    //cheese_listening:item:get powoduje ze na pojedynczym itemie zmieniamy fetchowane pola, itemOperations to jest to
    itemOperations: ['get' => ['normalization_context' => ['groups' => ['cheese_listening:read', 'cheese_listening:item:get']]],


        'put', 'delete'],
    attributes: ['pagination_items_per_page' => 10],
    denormalizationContext: ['groups' => 'cheese_listening:write'],
    normalizationContext: ['groups' => 'cheese_listening:read']
)]
#[ApiFilter(BooleanFilter::class, properties: ['isPublished'])]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'description' => 'partial',
    //owner, to filtr po tym, ze mozemy wyszukac po konkrenym IRI (user)
    'owner'=>'exact',
    'owner.username'=>'partial'

])]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
//do konkretnych pol, filter properties =nazwa[]
#[ApiFilter(PropertyFilter::class)]
class CheeseListening
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['cheese_listening:read', 'cheese_listening:write', 'user:read','user:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your first name must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private $description;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cheese_listening:read', 'cheese_listening:write', 'user:read','user:write'])]
    #[Assert\NotBlank]
    private $price;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'boolean')]
    private $isPublished = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cheeseListenings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cheese_listening:read', 'cheese_listening:write'])]
    #[Assert\Valid]
    private $owner;

    public function __construct(string $title = null)
    {
        $this->title = $title;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Groups(['cheese_listening:read'])]
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 40) {
            return $this->description;
        }
        return substr($this->description, 0, 40) . '...';
    }

    #[Groups(['cheese_listening:write','user:write'])]
    #[SerializedName('description')]
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * How long
     */
    #[Groups(['cheese_listening:read'])]
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }


    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
