<?php

declare(strict_types=1);

namespace App\Tests\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Doctrine\CompanyIsolationExtension;
use App\Entity\Interface\DepotAwareInterface;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\CompanyDepot;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class CompanyIsolationExtensionTest extends TestCase
{
    private Security $security;
    private CompanyIsolationExtension $extension;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->extension = new CompanyIsolationExtension($this->security);
    }

    public function testApplyToCollectionWithNoDepotAwareResource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToCollection($queryBuilder, $this->createMock(QueryNameGeneratorInterface::class), 'NotDepotAware');
    }

    public function testApplyToCollectionWithAdminNoCompany(): void
    {
        $user = new User(); // company is null
        $this->security->method('getUser')->willReturn($user);
        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(true);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('1 = 0')
            ->willReturnSelf();

        $this->extension->applyToCollection(
            $queryBuilder, 
            $this->createMock(QueryNameGeneratorInterface::class), 
            $this->createMock(DepotAwareInterface::class)::class
        );
    }

    public function testApplyToCollectionWithAdminValidCompany(): void
    {
        $company = new Company();
        $user = new User();
        $user->setCompany($company);
        
        $this->security->method('getUser')->willReturn($user);
        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(true);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        
        $queryBuilder->expects($this->once())
            ->method('join')
            ->with('o.companyDepot', 'cd')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('cd.company = :company')
            ->willReturnSelf();

        $this->extension->applyToCollection(
            $queryBuilder, 
            $this->createMock(QueryNameGeneratorInterface::class), 
            $this->createMock(DepotAwareInterface::class)::class
        );
    }

    public function testApplyToCollectionWithUserNoDepot(): void
    {
        $user = new User(); // depot is null
        $this->security->method('getUser')->willReturn($user);
        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(false);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('1 = 0')
            ->willReturnSelf();

        $this->extension->applyToCollection(
            $queryBuilder, 
            $this->createMock(QueryNameGeneratorInterface::class), 
            $this->createMock(DepotAwareInterface::class)::class
        );
    }
}
