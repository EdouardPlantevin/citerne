<?php

declare(strict_types=1);

namespace App\State\GlobalSettings;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\EditGlobalSettings;
use App\Entity\GlobalSettings;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GlobalSettingsEditProcessor implements ProcessorInterface
{

    public function __construct(
        private Security               $security,
        private EntityManagerInterface $manager,
    ){}


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GlobalSettings
    {

        if (!$data instanceof EditGlobalSettings) {
            throw new \InvalidArgumentException('Le payload doit être de type EditGlobalSettings');
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }

        $company = $user->getCompany();

        if (null === $company) {
            throw new NotFoundHttpException('Aucune société n\'a été trouvée pour cet utilisateur.');
        }

        $globalSettings = $company->getGlobalSettings();

        if (null === $globalSettings) {
            throw new NotFoundHttpException('Aucun réglage global n\'a encore été configuré pour votre entreprise');
        }

        if (null !== $data->loadingTime) {
            $globalSettings->setLoadingTime($data->loadingTime);
        }

        if (null !== $data->averageSpeed) {
            $globalSettings->setAverageSpeed($data->averageSpeed);
        }

        if (null !== $data->unloadingTime) {
            $globalSettings->setUnloadingTime($data->unloadingTime);
        }

        $this->manager->flush();

        return $globalSettings;
    }
}
