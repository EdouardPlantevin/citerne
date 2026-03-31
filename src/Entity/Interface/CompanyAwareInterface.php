<?php

declare(strict_types=1);

namespace App\Entity\Interface;

use App\Entity\Company;

interface CompanyAwareInterface
{
    public function getCompany(): ?Company;
}
