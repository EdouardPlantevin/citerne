<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\CompanyDepotRepository;
use App\Security\Voter\CompanyDepotVoter;
use App\State\CompanyDepot\CompanyDepotItemProvider;
use App\State\CompanyDepot\CompanyDepotItemsProvider;
use App\State\CompanyDepot\CompanyDepotProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CompanyDepotRepository::class)]
#[ApiResource(
    shortName: 'CompanyDepot',
    description: 'Gestion des dépot de l\'entreprise',
    operations: [
        new GetCollection(
            uriTemplate: '/company_depots/all',
            security: "is_granted('" . User::ROLE_COMPANY_ADMIN . "')",
            provider: CompanyDepotItemsProvider::class
        ),
        new Get(
            uriTemplate: '/company_depots/me',
            provider: CompanyDepotItemProvider::class
        ),
        new Get(),
        new Post(
            security: "is_granted('" . User::ROLE_COMPANY_ADMIN . "')",
            processor: CompanyDepotProcessor::class
        ),
        new Patch(
            security: "is_granted('" . CompanyDepotVoter::EDIT . "')",
            securityMessage: "Ce dépot ne vous appartient pas.",
        ),
        new Delete(
            security: "is_granted('" . CompanyDepotVoter::DELETE . "')",
            securityMessage: "Vous ne pouvez pas spprimer un dépôt d'une autre société"
        )
    ],
    normalizationContext: ['groups' => ['company_depot:read']],
    denormalizationContext: ['groups' => ['company_depot:write']]
)]
class CompanyDepot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['company_depot:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company_depot:read', 'company_depot:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company_depot:read', 'company_depot:write'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company_depot:read', 'company_depot:write'])]
    private ?string $openingHours = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company_depot:read', 'company_depot:write'])]
    private ?string $closingHours = null;

    #[ORM\ManyToOne(inversedBy: 'companyDepots')]
    private ?Company $company = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'companyDepot')]
    private Collection $users;

    /**
     * @var Collection<int, Driver>
     */
    #[ORM\OneToMany(targetEntity: Driver::class, mappedBy: 'companyDepot')]
    private Collection $drivers;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->drivers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getOpeningHours(): ?string
    {
        return $this->openingHours;
    }

    public function setOpeningHours(?string $openingHours): static
    {
        $this->openingHours = $openingHours;

        return $this;
    }

    public function getClosingHours(): ?string
    {
        return $this->closingHours;
    }

    public function setClosingHours(?string $closingHours): static
    {
        $this->closingHours = $closingHours;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCompanyDepot($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompanyDepot() === $this) {
                $user->setCompanyDepot(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Driver>
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): static
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers->add($driver);
            $driver->setCompanyDepot($this);
        }

        return $this;
    }

    public function removeDriver(Driver $driver): static
    {
        if ($this->drivers->removeElement($driver)) {
            // set the owning side to null (unless already changed)
            if ($driver->getCompanyDepot() === $this) {
                $driver->setCompanyDepot(null);
            }
        }

        return $this;
    }
}
