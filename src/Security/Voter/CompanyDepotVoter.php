<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\CompanyDepot;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CompanyDepotVoter extends Voter
{

    public const EDIT = 'COMPANY_DEPOT_EDIT';
    public const DELETE = 'COMPANY_DEPOT_DELETE';

    public function __construct(
        private Security $security,
    ) {
    }

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

        if (!$this->security->isGranted(User::ROLE_COMPANY_ADMIN)) {
            return false;
        }

        $userCompany = $user->getCompany();
        $depotCompany = $depot->getCompany();

        if ($userCompany === null || $depotCompany === null) {
            return false;
        }

        return $userCompany === $depotCompany;

    }
}
