<?php

declare(strict_types=1);

namespace App\Tests\State\Driver;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Company;
use App\Entity\CompanyDepot;
use App\Entity\Driver;
use App\Entity\User;
use App\State\Driver\DriverCreateProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DriverCreateProcessorTest extends TestCase
{
    private ProcessorInterface $persistProcessor;
    private Security $security;
    private DriverCreateProcessor $processor;

    protected function setUp(): void
    {
        $this->persistProcessor = $this->createMock(ProcessorInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->processor = new DriverCreateProcessor($this->persistProcessor, $this->security);
    }

    public function testAdminFailsIfNoDepotChosen(): void
    {
        $user = new User();
        $this->security->method('getUser')->willReturn($user);
        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(true);

        $driver = new Driver(); // depot is null
        $this->expectException(BadRequestHttpException::class);

        $this->processor->process($driver, $this->createMock(Operation::class));
    }

    public function testAdminFailsIfDepotBelongsToOtherCompany(): void
    {
        $myCompany = new Company();
        $otherCompany = new Company();
        
        $user = new User();
        $user->setCompany($myCompany);
        
        $this->security->method('getUser')->willReturn($user);
        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(true);

        $depot = new CompanyDepot();
        $depot->setCompany($otherCompany);

        $driver = new Driver();
        $driver->setCompanyDepot($depot);

        $this->expectException(AccessDeniedHttpException::class);

        $this->processor->process($driver, $this->createMock(Operation::class));
    }

    public function testUserSetsOwnDepot(): void
    {
        $depot = new CompanyDepot();
        $user = new User();
        $user->setCompanyDepot($depot);

        $this->security->method('getUser')->willReturn($user);
        $this->security->method('isGranted')->with(User::ROLE_COMPANY_ADMIN)->willReturn(false);

        $driver = new Driver();
        $operation = $this->createMock(Operation::class);

        $this->persistProcessor->expects($this->once())
            ->method('process')
            ->willReturn($driver);

        $this->processor->process($driver, $operation);

        $this->assertSame($depot, $driver->getCompanyDepot());
    }
}
