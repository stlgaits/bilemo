<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ApiResource(
    collectionOperations: [
        "get",
        "post"
    ],
    itemOperations: [
        "get",
        "delete" => [
            "security" => "is_granted('DELETE_CUSTOMER', object)",
            'security_message' => 'Sorry, you can only delete Customers linked to your own Account.',
            ],
     ],
    attributes: [
        'pagination_items_per_page' => 10,
        'formats' => ['json', 'jsonld'],
    ],
    denormalizationContext: ['groups' => [ 'customer:write']],
    normalizationContext: ['groups' => [ 'customer:read']],
)]
#[UniqueEntity(fields: ['email'])]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['customer:read'])]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['customer:read', 'customer:write'])]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    private ?string $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['customer:read', 'customer:write'])]
    #[Assert\NotBlank()]
    private ?string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['customer:read', 'customer:write'])]
    #[Assert\NotBlank()]
    private ?string $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $phoneNumber;

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'], inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['customer:read'])]
    #[Assert\Valid()]
    private ?Account $account;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Groups(['customer:read'])]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'update', field: ['email', 'phoneNumber', 'account', 'firstName', 'lastName'])]
    #[Groups(['customer:read'])]
    private ?\DateTimeImmutable $updatedAt;

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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
