<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\RegisterUser;
use App\Domain\User\UserRegistrar;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserRegistrationProcessor implements ProcessorInterface
{

    public function __construct(
        private UserRegistrar $userRegistrar
    ){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof RegisterUser) {
            return;
        }

        $this->userRegistrar->register(
            email: $data->email,
            plainPassword: $data->plainPassword,
            lastname: $data->lastname,
            firstname: $data->firstname,
            phone: $data->phone
        );
    }

}
