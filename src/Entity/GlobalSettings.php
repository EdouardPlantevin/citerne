<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\EditGlobalSettings;
use App\Repository\GlobalSettingsRepository;
use App\State\GlobalSettings\GlobalSettingsCreateProcessor;
use App\State\GlobalSettings\GlobalSettingsEditProcessor;
use App\State\GlobalSettings\GlobalSettingsItemProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    shortName: 'GlobalSettings',
    description: 'Gestion des variables globales de l\'entreprise',
    operations: [
        new Get(
            uriTemplate: 'global_settings',
            provider: GlobalSettingsItemProvider::class
        ),
        new Post(
            security: "is_granted('" . User::ROLE_COMPANY_ADMIN . "')",
            processor: GlobalSettingsCreateProcessor::class
        ),
        new Patch(
            uriTemplate: 'global_settings',
            security: "is_granted('ROLE_COMPANY_ADMIN')",
            input: EditGlobalSettings::class,
            processor: GlobalSettingsEditProcessor::class
        )
    ],
    normalizationContext: ['groups' => ['global_settings:read']],
    denormalizationContext: ['groups' => ['global_settings:write']]
)]
#[ORM\Entity(repositoryClass: GlobalSettingsRepository::class)]
class GlobalSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['global_settings:write', 'global_settings:read'])]
    private ?int $averageSpeed = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['global_settings:write', 'global_settings:read'])]
    private ?int $loadingTime = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['global_settings:write', 'global_settings:read'])]
    private ?int $unloadingTime = null;

    #[ORM\OneToOne(inversedBy: 'globalSettings', cascade: ['persist', 'remove'])]
    private ?Company $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAverageSpeed(): ?int
    {
        return $this->averageSpeed;
    }

    public function setAverageSpeed(?int $averageSpeed): static
    {
        $this->averageSpeed = $averageSpeed;

        return $this;
    }

    public function getLoadingTime(): ?int
    {
        return $this->loadingTime;
    }

    public function setLoadingTime(?int $loadingTime): static
    {
        $this->loadingTime = $loadingTime;

        return $this;
    }

    public function getUnloadingTime(): ?int
    {
        return $this->unloadingTime;
    }

    public function setUnloadingTime(?int $unloadingTime): static
    {
        $this->unloadingTime = $unloadingTime;

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
}
