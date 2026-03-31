<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Interface\DepotAwareInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class DepotResourceVoter extends Voter
{

    public const VIEW = 'DEPOT_RESOURCE_VIEW';
    public const EDIT = 'DEPOT_RESOURCE_EDIT';
    public const DELETE = 'DEPOT_RESOURCE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW], true)
            && $subject instanceof DepotAwareInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var DepotAwareInterface $subject */
        $resourceDepot = $subject->getCompanyDepot();

        if (null === $resourceDepot) {
            return false;
        }

        if (in_array(User::ROLE_COMPANY_ADMIN, $user->getRoles(), true)) {
            // SCENARIO: ROLE_COMPANY_ADMIN
            return $user->getCompany() === $resourceDepot->getCompany();
        } else {
            // SCENARIO: ROLE_USER
            $userDepot = $user->getCompanyDepot();

            if (null === $userDepot) {
                return false;
            }

            return $userDepot === $resourceDepot;
        }

    }
}
