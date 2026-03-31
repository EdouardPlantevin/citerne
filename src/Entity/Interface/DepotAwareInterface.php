<?php

declare(strict_types=1);

namespace App\Entity\Interface;

use App\Entity\CompanyDepot;

interface DepotAwareInterface
{
    public function getCompanyDepot(): ?CompanyDepot;
}
