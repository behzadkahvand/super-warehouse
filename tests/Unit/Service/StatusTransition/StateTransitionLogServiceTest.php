<?php

namespace App\Tests\Unit\Service\StatusTransition;

use App\Dictionary\ReceiptStatusDictionary;
use App\Document\StatusTransitionLog\AdminLogData;
use App\Document\StatusTransitionLog\ReceiptItemStatusLog;
use App\Entity\Admin;
use App\Entity\ReceiptItem;
use App\Service\StatusTransition\Exceptions\ODMStateLogClassNotFoundException;
use App\Service\StatusTransition\StateTransitionLogService;
use App\Service\StatusTransition\TransitionableInterface;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Mockery;
use ReflectionClass;

class StateTransitionLogServiceTest extends BaseUnitTestCase
{
    protected DocumentManager|Mockery\LegacyMockInterface|Mockery\MockInterface|null $documentManager;

    protected StateTransitionLogService|Mockery\MockInterface|null $stateTransitionLogService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentManager = Mockery::mock(DocumentManager::class);

        $this->stateTransitionLogService = Mockery::mock(StateTransitionLogService::class, [
            $this->documentManager,
        ])
                                                  ->makePartial()
                                                  ->shouldAllowMockingProtectedMethods();
    }

    public function testAddLogSuccessfully(): void
    {
        $receiptItemMock = Mockery::mock(ReceiptItem::class);
        $receiptItemMock->shouldReceive('getId')
                        ->once()
                        ->withNoArgs()
                        ->andReturn(1);

        $statusFrom = ReceiptStatusDictionary::DRAFT;
        $statusTo   = ReceiptStatusDictionary::APPROVED;

        $reflectionClass  = new ReflectionClass($this->stateTransitionLogService);
        $reflectionMethod = $reflectionClass->getMethod('clearLogs');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invokeArgs(
            $this->stateTransitionLogService,
            []
        );

        $this->stateTransitionLogService->addLog($receiptItemMock, $statusFrom, $statusTo);

        $reflectionClass  = new ReflectionClass($this->stateTransitionLogService);
        $reflectionMethod = $reflectionClass->getMethod('getLogs');
        $reflectionMethod->setAccessible(true);
        $logs = $reflectionMethod->invokeArgs(
            $this->stateTransitionLogService,
            []
        );

        self::assertEquals(1, count($logs));
        $receiptItemTransitionLog = ReceiptItemStatusLog::class;
        self::assertArrayHasKey($receiptItemTransitionLog, $logs);
        self::assertEquals(1, count($logs[$receiptItemTransitionLog]));

        self::assertEquals(1, $logs[$receiptItemTransitionLog][0]['entityId']);
        self::assertEquals($statusFrom, $logs[$receiptItemTransitionLog][0]['statusFrom']);
        self::assertEquals($statusTo, $logs[$receiptItemTransitionLog][0]['statusTo']);
    }

    public function testAddLogStatusLogClassNotFound(): void
    {
        $transitionable = Mockery::mock(TransitionableInterface::class);

        $statusFrom = ReceiptStatusDictionary::DRAFT;
        $statusTo   = ReceiptStatusDictionary::APPROVED;

        self::expectException(ODMStateLogClassNotFoundException::class);

        $this->stateTransitionLogService->addLog($transitionable, $statusFrom, $statusTo);
    }

    public function testGetUserWhenUserExist(): void
    {
        $id       = 1;
        $name     = "test1";
        $family   = "test2";
        $username = "test@test.com";

        $admin = Mockery::mock(Admin::class);
        $admin->shouldReceive('getId')
              ->once()
              ->withNoArgs()
              ->andReturn($id);
        $admin->shouldReceive('getName')
              ->once()
              ->withNoArgs()
              ->andReturn($name);
        $admin->shouldReceive('getFamily')
              ->once()
              ->withNoArgs()
              ->andReturn($family);
        $admin->shouldReceive('getUsername')
              ->once()
              ->withNoArgs()
              ->andReturn($username);

        $this->stateTransitionLogService->setUser($admin);

        $reflectionClass  = new ReflectionClass($this->stateTransitionLogService);
        $reflectionMethod = $reflectionClass->getMethod('getUser');
        $reflectionMethod->setAccessible(true);

        /** @var AdminLogData $result */
        $result = $reflectionMethod->invokeArgs(
            $this->stateTransitionLogService,
            []
        );

        self::assertInstanceOf(AdminLogData::class, $result);
        self::assertEquals($id, $result->getId());
        self::assertEquals($name . " " . $family, $result->getName());
        self::assertEquals($username, $result->getUsername());
    }

    public function testItCanPersist(): void
    {
        $reflectionClass  = new ReflectionClass($this->stateTransitionLogService);
        $reflectionMethod = $reflectionClass->getMethod('clearLogs');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invokeArgs(
            $this->stateTransitionLogService,
            []
        );

        $admin = Mockery::mock(AdminLogData::class);
        $this->stateTransitionLogService->shouldReceive('getUser')
                                        ->once()
                                        ->withNoArgs()
                                        ->andReturn($admin);

        $receipt = Mockery::mock(ReceiptItem::class);
        $receipt->shouldReceive('getId')
                    ->twice()
                    ->withNoArgs()
                    ->andReturn(1, 2);

        $this->stateTransitionLogService
            ->addLog(
                $receipt,
                ReceiptStatusDictionary::DRAFT,
                ReceiptStatusDictionary::APPROVED
            )
            ->addLog(
                $receipt,
                ReceiptStatusDictionary::DRAFT,
                ReceiptStatusDictionary::APPROVED
            );

        $this->documentManager->shouldReceive('persist')
            ->twice()
            ->with(Mockery::type(ReceiptItemStatusLog::class))
            ->andReturn();

        $this->documentManager->shouldReceive('flush')
                              ->once()
                              ->withNoArgs()
                              ->andReturn();

        $this->stateTransitionLogService->persist();
    }
}
