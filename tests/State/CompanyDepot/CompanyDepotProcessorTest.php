<?php

declare(strict_types=1);

namespace App\Tests\State\CompanyDepot;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Company;
use App\Entity\CompanyDepot;
use App\Entity\User;
use App\State\CompanyDepot\CompanyDepotProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CompanyDepotProcessorTest extends TestCase
{
    private ProcessorInterface $persistProcessor;
    private Security $security;
    private CompanyDepotProcessor $processor;

    protected function setUp(): void
    {
        $this->persistProcessor = $this->createMock(ProcessorInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->processor = new CompanyDepotProcessor($this->persistProcessor, $this->security);
    }

    public function testProcessThrowsIfUserNotLoggedIn(): void
    {
        $this->security->method('getUser')->willReturn(null);
        $this->expectException(AccessDeniedHttpException::class);
        
        $this->processor->process(new CompanyDepot(), $this->createMock(Operation::class));
    }

    public function testProcessThrowsIfUserHasNoCompany(): void
    {
        $user = new User(); // company is null
        $this->security->method('getUser')->willReturn($user);
        $this->expectException(BadRequestHttpException::class);
        
        $this->processor->process(new CompanyDepot(), $this->createMock(Operation::class));
    }

    public function testProcessSetsCompanyAndPersists(): void
    {
        $company = new Company();
        $user = new User();
        $user->setCompany($company);
        
        $this->security->method('getUser')->willReturn($user);
        
        $depot = new CompanyDepot();
        $operation = $this->createMock(Operation::class);

        $this->persistProcessor->expects($this->once())
            ->method('process')
            ->with($depot, $operation)
            ->willReturn($depot);

        $result = $this->processor->process($depot, $operation);

        $this->assertSame($company, $depot->getCompany());
        $this->assertSame($depot, $result);
    }
}
