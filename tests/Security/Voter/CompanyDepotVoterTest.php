<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use App\Entity\Company;
use App\Entity\CompanyDepot;
use App\Entity\User;
use App\Security\Voter\CompanyDepotVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CompanyDepotVoterTest extends TestCase
{
    private Security $security;
    private CompanyDepotVoter $voter;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->voter = new CompanyDepotVoter($this->security);
    }

    public function testVoteDeniesIfUserNotLoggedIn(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        $depot = new CompanyDepot();

        $result = $this->voter->vote($token, $depot, [CompanyDepotVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVoteDeniesIfUserNotAdmin(): void
    {
        $user = new User();
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(false);

        $depot = new CompanyDepot();

        $result = $this->voter->vote($token, $depot, [CompanyDepotVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVoteDeniesIfCompanyIsNull(): void
    {
        $user = new User(); // company is null
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(true);

        $depot = new CompanyDepot(); // company is null

        $result = $this->voter->vote($token, $depot, [CompanyDepotVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVoteDeniesIfCompaniesDoNotMatch(): void
    {
        $company1 = $this->createMock(Company::class);
        $company2 = $this->createMock(Company::class);

        $user = new User();
        $user->setCompany($company1);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(true);

        $depot = new CompanyDepot();
        $depot->setCompany($company2);

        $result = $this->voter->vote($token, $depot, [CompanyDepotVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVoteGrantsIfCompaniesMatchAndAdmin(): void
    {
        $company = $this->createMock(Company::class);

        $user = new User();
        $user->setCompany($company);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(true);

        $depot = new CompanyDepot();
        $depot->setCompany($company);

        $result = $this->voter->vote($token, $depot, [CompanyDepotVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }
}
