<?php

namespace App\Services;

use App\Jobs\DeliverOrder;
use App\Models\Order;
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

    /**
     * @param Order $order
     * @return bool
     * @throws \Exception
     */
    public function processNextOrder(Order $order)
    {
        try {

            $this->db->beginTransaction();

            $this->orderRepo->order = $order;

            $waiterId = $this->waiterRepo->getOneAvailable()->getAttribute('id') ?? null;

            $this->waiterRepo->setUnavailable($waiterId);

            $statusPreparation = $this->orderRepo->prepareOrder($waiterId);

            if (true !== $statusPreparation) {
                $exceptionMessage = sprintf(
                    'Could not prepare the order: %s',
                    print_r($this->orderRepo->order->getAttributes(), true)
                );

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            $sommelierId = $this->sommelierRepo->getOneAvailable()->getAttribute('id') ?? null;

            $statusSommelier = $this->orderRepo->sendToSommelier($sommelierId);

            if (true !== $statusSommelier) {
                $exceptionMessage = sprintf(
                    'Could not send the order to sommelier: %s',
                    print_r($this->orderRepo->order->getAttributes(), true)
                );

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            $this->sommelierRepo->setUnavailable($sommelierId);

            $statusAvailability = $this->orderRepo->processAvailabilityOfWines();

            if (true !== $statusAvailability) {
                $exceptionMessage = sprintf(
                    'Could not process availability of wines: %s',
                    print_r($this->orderRepo->order->getAttributes(), true)
                );

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            DeliverOrder::dispatch($this->orderRepo->order);

            $this->sommelierRepo->setAvailable($sommelierId);

            $this->waiterRepo->setAvailable($waiterId);

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollBack();

            throw $e;

        }

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     * @throws \Exception
     */
    public function deliverOrder(Order $order)
    {
        try {

            $this->db->beginTransaction();

            $this->orderRepo->order = $order;

            $status = $this->orderRepo->deliverOrder();

            if (true !== $status) {
                $exceptionMessage = sprintf(
                    'Could not deliver the order: %s',
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
