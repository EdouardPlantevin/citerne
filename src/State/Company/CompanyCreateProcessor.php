<?php

declare(strict_types=1);

namespace App\State\Company;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class CompanyCreateProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security $security,
        private EntityManagerInterface $manager,
    ){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();

        if(!$user instanceof User){
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }

        if ($user->getCompany()) {
            throw new BadRequestHttpException('Vous avez déjà une société');
        }

        $company = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        $user->setCompany($company);
        $this->manager->flush();

        return $company;
    }
}
