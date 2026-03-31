<?php

declare(strict_types=1);

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\CreateEmployee;
use App\Entity\User;
use App\Repository\CompanyDepotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class EmployeeCreateProcessor implements ProcessorInterface
{

    public function __construct(
        private Security $security,
        private EntityManagerInterface $manager,
        private UserPasswordHasherInterface $passwordHasher,
        private CompanyDepotRepository $companyDepotRepository,
    ){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {

        if (!$data instanceof CreateEmployee) {
            return;
        }

        /** @var User $boss */
        $boss = $this->security->getUser();
        $company = $boss->getCompany();


        //TODO: Créer sa société à l'inscription
        if(null === $company) {
            throw new BadRequestHttpException('Vous devez d\'abord créer votre société');
        }


        $companyDepot = $this->companyDepotRepository->findOneBy(['id' => $data->companyDepotId, 'company' => $company]);

        if (null === $companyDepot) {
            throw new BadRequestHttpException('Le dépot n\'a pas été trouvé');
        }

        $employee = new User();
        $employee->setEmail($data->email)
                ->setFirstName($data->firstName)
                ->setLastName($data->lastName)
                ->setCompany($company)
                ->setPhone($data->phone)
                ->setCompanyDepot($companyDepot)
                ->setRoles([User::ROLE_USER]);

        $employee->setPassword($this->passwordHasher->hashPassword($employee, $data->plainPassword));

        $this->manager->persist($employee);
        $this->manager->flush();
    }
}
