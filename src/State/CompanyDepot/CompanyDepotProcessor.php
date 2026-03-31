<?php

declare(strict_types=1);

namespace App\State\CompanyDepot;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\CompanyDepot;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @implements ProcessorInterface<CompanyDepot, CompanyDepot>
 */
final readonly class CompanyDepotProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security           $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): CompanyDepot
    {
        if (!$data instanceof CompanyDepot) {
            throw new \InvalidArgumentException('Type de donnée invalide.');
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }

        $company = $user->getCompany();
        if ($company === null) {
            throw new BadRequestHttpException('Vous devez créer une société pour créer un depot.');
        }

        $data->setCompany($company);

        /** @var CompanyDepot $companyDepot */
        $companyDepot = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        return $companyDepot;
    }
}
