<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Interface\DepotAwareInterface;
use App\Repository\DriverRepository;
use App\Security\Voter\DepotResourceVoter;
use App\State\Driver\DriverCreateProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('" . DepotResourceVoter::VIEW . "', object)",
        ),
        new Post(
            processor: DriverCreateProcessor::class
        ),
        new GetCollection(),
        new Patch(
            security: "is_granted('" . DepotResourceVoter::EDIT . "', object)",
            securityMessage: "Vous n'avez pas les droits",
        ),
        new Delete(
            security: "is_granted('" . DepotResourceVoter::DELETE . "', object)",
            securityMessage: "Vous ne pouvez pas supprimer un conducteur qui ne fais pas partir de votre société"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['companyDepot' => 'exact'])]
class Driver implements DepotAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(nullable: true)]
    private ?int $workedTime = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'drivers')]
    private ?CompanyDepot $companyDepot = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getWorkedTime(): ?int
    {
        return $this->workedTime;
    }

    public function setWorkedTime(?int $workedTime): static
    {
        $this->workedTime = $workedTime;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCompanyDepot(): ?CompanyDepot
    {
        return $this->companyDepot;
    }

    public function setCompanyDepot(?CompanyDepot $companyDepot): static
    {
        $this->companyDepot = $companyDepot;

        return $this;
    }
}
