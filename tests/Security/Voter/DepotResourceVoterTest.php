<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use App\Entity\Company;
use App\Entity\CompanyDepot;
use App\Entity\Interface\DepotAwareInterface;
use App\Entity\User;
use App\Security\Voter\DepotResourceVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class DepotResourceVoterTest extends TestCase
{
    private Security $security;
    private DepotResourceVoter $voter;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->voter = new DepotResourceVoter($this->security);
    }

    public function testVoteDeniesIfUserNotLoggedIn(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        $resource = $this->createMock(DepotAwareInterface::class);

        $result = $this->voter->vote($token, $resource, [DepotResourceVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testAdminGrantsIfCompaniesMatch(): void
    {
        $company = $this->createMock(Company::class);
        $user = new User();
        $user->setCompany($company);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(true);

        $depot = new CompanyDepot();
        $depot->setCompany($company);

        $resource = $this->createMock(DepotAwareInterface::class);
        $resource->method('getCompanyDepot')->willReturn($depot);

        $result = $this->voter->vote($token, $resource, [DepotResourceVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testAdminDeniesIfCompaniesDoNotMatch(): void
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

        $resource = $this->createMock(DepotAwareInterface::class);
        $resource->method('getCompanyDepot')->willReturn($depot);

        $result = $this->voter->vote($token, $resource, [DepotResourceVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testUserGrantsIfDepotsMatch(): void
    {
        $depot = new CompanyDepot();
        $user = new User();
        $user->setCompanyDepot($depot);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(false);

        $resource = $this->createMock(DepotAwareInterface::class);
        $resource->method('getCompanyDepot')->willReturn($depot);

        $result = $this->voter->vote($token, $resource, [DepotResourceVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserDeniesIfDepotsDoNotMatch(): void
    {
        $depot1 = new CompanyDepot();
        $depot2 = new CompanyDepot();
        $user = new User();
        $user->setCompanyDepot($depot1);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(false);

        $resource = $this->createMock(DepotAwareInterface::class);
        $resource->method('getCompanyDepot')->willReturn($depot2);

        $result = $this->voter->vote($token, $resource, [DepotResourceVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }
}
