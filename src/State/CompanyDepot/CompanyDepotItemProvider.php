<?php

declare(strict_types=1);

namespace App\State\CompanyDepot;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


final readonly class CompanyDepotItemProvider implements ProviderInterface
{

    public function __construct(
        private Security $security,
    ){}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Vous devez être connecté.');
        }

        $company = $user->getCompany();

        if (null === $company) {
            throw new NotFoundHttpException('Vous n\'êtes rattaché à aucune société');
        }

        $depot = $company->getCompanyDepots()->first();

        if (!$depot) {
            throw new NotFoundHttpException('Aucun dépôt n\'a été configuré pour votre société');
        }

        return $depot;

    }
}
