<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserRegistrar
{

    public function __construct(
        private EntityManagerInterface      $manager,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function register(string $email, string $plainPassword, string $lastname, ?string $firstname = null, ?string $phone = null): User
    {
        $user = new User();

        $user->setEmail($email)
            ->setLastname($lastname)
            ->setFirstname($firstname)
            ->setPhone($phone)
            ->setRoles([User::ROLE_COMPANY_ADMIN]);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;

    }


}
