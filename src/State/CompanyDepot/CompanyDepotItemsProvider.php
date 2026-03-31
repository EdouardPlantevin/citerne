<?php

declare(strict_types=1);

namespace App\State\CompanyDepot;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\CompanyDepot;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Uniquement pour les ROLE_COMPANY_ADMIN
 */
final readonly class CompanyDepotItemsProvider implements ProviderInterface
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

        return $company->getCompanyDepots();
    }
}
