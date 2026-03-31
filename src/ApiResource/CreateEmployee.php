<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\User;
use App\State\User\EmployeeCreateProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Employee',
    operations: [
        new Post(
            uriTemplate: '/employees',
            security: "is_granted('" . User::ROLE_COMPANY_ADMIN . "')",
            processor: EmployeeCreateProcessor::class,
        )
    ]
)]
final class CreateEmployee
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        public string $plainPassword,

        #[Assert\NotBlank]
        public string $firstName,

        #[Assert\NotBlank]
        public string $lastName,

        #[Assert\NotBlank]
        public string $phone,

        #[Assert\NotBlank]
        public int $companyDepotId,
    ){}
}
