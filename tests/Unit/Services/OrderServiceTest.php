<?php

namespace Tests\Unit\Services;

use App\Jobs\DeliverOrder;
use App\Models\Order;
use App\Models\Sommelier;
use App\Models\Waiter;
use App\Models\WineOrder;
use App\Repositories\OrderRepository;
use App\Repositories\SommelierRepository;
use App\Repositories\WaiterRepository;
use App\Services\OrderService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Queue;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @coversDefaultClass \App\Services\OrderService
 */
class OrderServiceTest extends TestCase
{
    /**
     * @var DatabaseManager|MockObject
     */
    private $db;

    /**
     * @var OrderRepository|MockObject
     */
    private $orderRepository;

    /**
     * @var WaiterRepository|MockObject
     */
    private $waiterRepository;

    /**
     * @var SommelierRepository|MockObject
     */
    private $sommelierRepository;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Waiter
     */
    private $waiter;

    /**
     * @var Sommelier
     */
    private $sommelier;

    /**
     * @var LogManager|MockObject
     */
    private $logManager;
    /**
     * @var TestHandler
     */
    private $loggerHandler;

    /**
     * @var OrderService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();

        $this->db = $this->createPartialMock(
            DatabaseManager::class,
            ['beginTransaction', 'commit', 'rollback']
        );

        $this->order = factory(Order::class)->make([
            'id'     => 1,
            'status' => 'preparing'
        ]);

        $this->waiter    = factory(Waiter::class)->make(['id' => 1]);
        $this->sommelier = factory(Sommelier::class)->make(['id' => 1]);

        $this->orderRepository     = $this->createMock(OrderRepository::class);
        $this->waiterRepository    = $this->createMock(WaiterRepository::class);
        $this->sommelierRepository = $this->createMock(SommelierRepository::class);

        $this->loggerHandler = new TestHandler();
        $this->logManager    = $this->createMock(LogManager::class);

        $this->logManager->method('channel')
            ->willReturn(new Logger('test', [$this->loggerHandler]));

        $this->service = new OrderService(
            $this->db,
            $this->orderRepository,
            $this->waiterRepository,
            $this->sommelierRepository,
            $this->logManager
        );
    }

    /**
     * @covers ::deliverOrder
     * @throws \Exception
     */
    public function testDeliverOrderSucceeds()
    {
        $this->db->expects(self::once())->method('beginTransaction');
        $this->db->expects(self::once())->method('commit');

        $this->orderRepository->expects(static::once())
            ->method('deliverOrder')
            ->willReturn(true);

        $this->service->deliverOrder($this->order);

        static::assertTrue($this->loggerHandler->hasInfoRecords());
        static::assertFalse($this->loggerHandler->hasErrorRecords());
    }

    /**
     * @covers ::deliverOrder
     * @throws \Exception
     */
    public function testDeliverOrderFails()
    {
        $this->db->expects(self::once())->method('beginTransaction');
        $this->db->expects(self::once())->method('rollback');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageRegExp('/Could not deliver and close the order/i');

        $this->orderRepository->expects(static::once())
            ->method('deliverOrder')
            ->willReturn(false);

        $this->service->deliverOrder($this->order);

        static::assertTrue($this->loggerHandler->hasErrorRecords());
        static::assertFalse($this->loggerHandler->hasInfoRecords());
    }

    /**
     * @covers ::processNextOrder
     * @throws \Exception
     */
    public function testProcessNextOrderSucceeds()
    {
        Queue::fake();

        $this->db->expects(self::once())->method('beginTransaction');
        $this->db->expects(self::once())->method('commit');

        $this->waiterRepository->expects(static::once())
            ->method('getOneAvailable')
            ->willReturn($this->waiter);

        $this->waiterRepository->expects(static::once())
            ->method('setUnavailable')
            ->with(1);

        $this->orderRepository->expects(static::once())
            ->method('prepareOrder')
            ->with(1)
            ->willReturn(true);

        $this->sommelierRepository->expects(static::once())
            ->method('getOneAvailable')
            ->willReturn($this->sommelier);

        $this->orderRepository->expects(static::once())
            ->method('sendToSommelier')
            ->with(1)
            ->willReturn(true);

        $this->sommelierRepository->expects(static::once())
            ->method('setUnavailable')
            ->with(1);

        $this->orderRepository->expects(static::once())
            ->method('processAvailabilityOfWines')
            ->willReturn(true);

        $this->sommelierRepository->expects(static::once())
            ->method('setAvailable')
            ->with(1)
            ->willReturn(true);

        $this->waiterRepository->expects(static::once())
            ->method('setAvailable')
            ->with(1)
            ->willReturn(true);

        $this->service->processNextOrder($this->order);

        static::assertTrue($this->loggerHandler->hasInfoRecords());
        static::assertFalse($this->loggerHandler->hasErrorRecords());
    }
}
