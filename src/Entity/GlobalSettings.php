<?php

namespace App\Entity;

use App\Repository\GlobalSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GlobalSettingsRepository::class)]
class GlobalSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $averageSpeed = null;

    #[ORM\Column(nullable: true)]
    private ?int $loadingTime = null;

    #[ORM\Column(nullable: true)]
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
