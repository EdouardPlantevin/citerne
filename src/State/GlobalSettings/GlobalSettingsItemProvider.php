<?php

declare(strict_types=1);

namespace App\State\GlobalSettings;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\RegisterUser;
use App\Entity\Company;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GlobalSettingsItemProvider implements ProviderInterface
{

    public function __construct(
        private readonly Security $security,
    ){}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Vous devez être connecté');
        }

        $company = $user->getCompany();

        if (null === $company) {
            throw new NotFoundHttpException('Vous n\'êtes rattaché à aucune société');
        }

        $globalSettings = $company->getGlobalSettings();

        if (null === $globalSettings) {
            throw new NotFoundHttpException('Aucun réglage global n\'a encore été configuré pour votre entreprise');
        }

        return $globalSettings;
    }
}
