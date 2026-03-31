<?php

declare(strict_types=1);

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Driver;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CompanyIsolationExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public function __construct(
        private Security $security,
    )
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {

        if (Driver::class !== $resourceClass) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (in_array(User::ROLE_COMPANY_ADMIN, $user->getRoles(), true)) {
            // Logique ROLE_COMPANY_ADMIN
            $company = $user->getCompany();

            $queryBuilder->join(sprintf('%s.companyDepot', $rootAlias), 'cd')
                        ->andWhere('cd.company = :company')
                        ->setParameter('company', $company);

        } else {
            // Logique ROLE_USER
            $myCompanyDepot = $user->getCompanyDepot();

            $queryBuilder->andWhere(sprintf('%s.companyDepot = :depot', $rootAlias))
                        ->setParameter('depot', $myCompanyDepot);
        }

    }
}
