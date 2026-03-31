<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\CompanyDepot;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CompanyDepotVoter extends Voter
{

    public const EDIT = 'COMPANY_DEPOT_EDIT';
    public const DELETE = 'COMPANY_DEPOT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof CompanyDepot;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var CompanyDepot $depot */
        $depot = $subject;

        return in_array(User::ROLE_COMPANY_ADMIN, $user->getRoles())
            && $user->getCompany() === $depot->getCompany();

    }
}
