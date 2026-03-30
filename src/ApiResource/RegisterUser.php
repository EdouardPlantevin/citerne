<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\UserRegistrationProcessor;
use Symfony\Component\Validator\Constraints as Assert;



#[ApiResource(
    shortName: 'User',
    operations: [
        new Post(
            uriTemplate: '/users/register',
            name: 'api_users_register',
            processor: UserRegistrationProcessor::class,
        )
    ]
)]
final class RegisterUser
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_WEAK)]
        public string $plainPassword,

        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        public string $lastname,

        #[Assert\Length(max: 255)]
        public ?string $firstname = null,

        #[Assert\Regex(pattern: '/^\+?[1-9]\d{1,14}$/', message: 'Le numéro de téléphone n\'est pas valide.')]
        public ?string $phone = null,
    ){}
}
