<?php

declare(strict_types=1);

namespace App\State\GlobalSettings;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GlobalSettingsCreateProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly Security               $security,
        private readonly EntityManagerInterface $manager,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface     $persistProcessor,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }

        $company = $user->getCompany();

        if (null === $company) {
            throw new NotFoundHttpException('Aucune société n\'a été trouvée pour cet utilisateur.');
        }

        if ($company->getGlobalSettings()) {
            throw new BadRequestHttpException('Vous avez déjà renseigné vos variables globales.');
        }

        $globalSettings = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        $globalSettings->setCompany($company);
        $this->manager->persist($globalSettings);
        $this->manager->flush();
    }
}
