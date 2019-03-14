<?php

namespace App\Services;

use App\Jobs\DeliverOrder;
use App\Jobs\ProcessOrder;
use App\Repositories\OrderRepository;
use App\Repositories\SommelierRepository;
use App\Repositories\WaiterRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Response;

class OrderService
{
    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * @var OrderRepository
     */
    protected $orderRepo;

    /**
     * @var WaiterRepository
     */
    protected $waiterRepo;

    /**
     * @var SommelierRepository
     */
    protected $sommelierRepo;

    public function __construct(DatabaseManager $db, OrderRepository $orderRepo, WaiterRepository $waiterRepo, SommelierRepository $sommelierRepo) {
        $this->db            = $db;
        $this->orderRepo     = $orderRepo;
        $this->waiterRepo    = $waiterRepo;
        $this->sommelierRepo = $sommelierRepo;
    }

    public function processNextOrder()
    {
        try {

            $this->db->beginTransaction();

            if (true !== $this->orderRepo->setNextOrderToProcess()) {
                return false;
            }

            $waiter = $this->orderRepo->order->getAttribute('waiter_id');

            $this->waiterRepo->setUnavailable($waiter);

            $status = $this->orderRepo->sendToSommelier($this->sommelierRepo->getOneAvailable());

            DeliverOrder::dispatchNow($this);

            $this->waiterRepo->setAvailable($waiter);

            if (true !== $status) {
                $exceptionMessage = sprintf(
                    'Could not send the order to sommelier: %s',
                    print_r($this->orderRepo->order->getAttributes(), true)
                );

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollBack();

            throw $e;

        }

        return true;
    }

    public function prepareNextOrder()
    {
        try {

            $this->db->beginTransaction();

            if (true !== $this->orderRepo->setNextOrderToPrepare()) {
                return false;
            }

            $sommelier = $this->orderRepo->order->getAttribute('sommelier_id');

            $this->sommelierRepo->setUnavailable($sommelier);

            $status = $this->orderRepo->checkAvailabilityOfWines();

            dd($status, $this->orderRepo->order);

            DeliverOrder::dispatchNow($this, 'deliveryToCustomer');

            $this->sommelierRepo->setAvailable($sommelier);

            if (true !== $status) {
                $exceptionMessage = sprintf(
                    'Could not check the availability of wines: %s',
                    print_r($this->orderRepo->order->getAttributes(), true)
                );

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollBack();

            throw $e;

        }

        return true;
    }
}
