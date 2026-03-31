<?php

declare(strict_types=1);

namespace App\State\Driver;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Driver;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class DriverCreateProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if (!$data instanceof Driver) {
            return $data;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Non authentifié');
        }

        if ($this->security->isGranted(User::ROLE_COMPANY_ADMIN)) {
            //Rôle ROLE_COMPANY_ADMIN
            $chosenDepot = $data->getCompanyDepot();

            if (null === $chosenDepot) {
                throw new BadRequestHttpException("Vous devez choisir un depot");
            }

            if ($chosenDepot->getCompany() !== $user->getCompany()) {
                throw new AccessDeniedHttpException('Ce dépôt n\'appartient pas à votre société');
            }

        } else {
            // Rôle classique
            $userDepot = $user->getCompanyDepot();

            if (null === $userDepot) {
                throw new BadRequestHttpException('Vous n\'êtes rattaché à aucun dépôt.');
            }

            $data->setCompanyDepot($userDepot);
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);

    }
}
