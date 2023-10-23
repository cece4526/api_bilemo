<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "detailUser",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getUsers")
 * )
 *
 *  * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "deleteUser",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getUsers")
 * )
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getUsers"])]
    private ?int $id = null;

    #[Groups(["getUsers"])]
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'ceci n\'est pas un email.',)]
    #[Assert\NotBlank(message: "L\'email est obligatoire")]
    #[Assert\Length(min: 1, max: 180, minMessage: "L\'email doit faire au moins {{ limit }} caractères", maxMessage: "L\'email ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $email = null;

    #[Groups(["getUsers"])]
    #[ORM\Column(type: "json")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(message: "Le Nom est obligatoire")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Le Nom doit faire au moins {{ limit }} caractères", maxMessage: "Le Nom ne peut pas faire plus de {{ limit }} caractères")]
    #[Groups(["getUsers"])]
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[Assert\NotBlank(message: "Le Prénom est obligatoire")]
    #[Assert\Length(min: 1, max: 255, minMessage: "Le Prénom doit faire au moins {{ limit }} caractères", maxMessage: "Le Prénom ne peut pas faire plus de {{ limit }} caractères")]
    #[Groups(["getUsers"])]
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    
    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Customer::class)]
    private Collection $customers;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getUsers"])]
    private ?Customer $Customer = null;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
     * Allows you to return the field that is used for authentication.
     *
     * @return string
     */
    public function getUsername(): string 
    {
        return $this->getUserIdentifier();
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): static
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->setUser($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): static
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getUser() === $this) {
                $customer->setUser(null);
            }
        }

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->Customer;
    }

    public function setCustomer(?Customer $Customer): static
    {
        $this->Customer = $Customer;

        return $this;
    }
}
