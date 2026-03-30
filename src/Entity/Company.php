<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\CompanyRepository;
use App\State\Company\CompanyCreateProcessor;
use App\State\Company\CompanyItemProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    shortName: 'Companies',
    description: 'Gestion des entreprises partenaires',
    operations: [
        new Get(
            uriTemplate: '/companies/me',
            provider: CompanyItemProvider::class
        ),
        new Post(
            processor: CompanyCreateProcessor::class
        )
    ],
    normalizationContext: ['groups' => ['company:read']],
    denormalizationContext: ['groups' => ['company:write']]
)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'company')]
    private Collection $users;

    #[ORM\OneToOne(mappedBy: 'company', cascade: ['persist', 'remove'])]
    private ?GlobalSettings $globalSettings = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }

    public function getGlobalSettings(): ?GlobalSettings
    {
        return $this->globalSettings;
    }

    public function setGlobalSettings(?GlobalSettings $globalSettings): static
    {
        if ($globalSettings === null && $this->globalSettings !== null) {
            $this->globalSettings->setCompany(null);
        }

        if ($globalSettings !== null && $globalSettings->getCompany() !== $this) {
            $globalSettings->setCompany($this);
        }

        $this->globalSettings = $globalSettings;

        return $this;
    }
}
