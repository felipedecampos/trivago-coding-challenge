<?php

namespace App\Services;

use App\Jobs\DeliverOrder;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\SommelierRepository;
use App\Repositories\WaiterRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Response;
use Illuminate\Log\LogManager;

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

    /**
     * @var LogManager
     */
    protected $logManager;

    public function __construct(
        DatabaseManager $db,
        OrderRepository $orderRepo,
        WaiterRepository $waiterRepo,
        SommelierRepository $sommelierRepo,
        LogManager $logManager
    ) {
        $this->db            = $db;
        $this->orderRepo     = $orderRepo;
        $this->waiterRepo    = $waiterRepo;
        $this->sommelierRepo = $sommelierRepo;
        $this->logManager    = $logManager;
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

            /**
             * Prepare the order
             */
            $waiterId = $this->waiterRepo->getOneAvailable()->getAttribute('id') ?? null;

            $this->logManager->channel('application')->info(
                'The waiter takes the order to prepare.',
                [
                    'waiterId' => $waiterId,
                    'order'    => $this->orderRepo->order->getAttributes()
                ]
            );

            $this->waiterRepo->setUnavailable($waiterId);

            $statusPreparation = $this->orderRepo->prepareOrder($waiterId);

            if (true !== $statusPreparation) {
                $exceptionMessage = sprintf(
                    'Could not prepare the order: %s',
                    print_r($this->orderRepo->order->getAttributes(), true)
                );

                $this->logManager->channel('application')->error($exceptionMessage);

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            /**
             * Send order to sommelier
             */
            $sommelierId = $this->sommelierRepo->getOneAvailable()->getAttribute('id') ?? null;

            $this->logManager->channel('application')->info(
                'The waiter sends the order to sommelier.',
                [
                    'order'       => $this->orderRepo->order->getAttributes(),
                    'sommelierId' => $sommelierId
                ]
            );

            $statusSommelier = $this->orderRepo->sendToSommelier($sommelierId);

            if (true !== $statusSommelier) {
                $exceptionMessage = sprintf(
                    'Could not send the order to sommelier: %s',
                    print_r($this->orderRepo->order->getAttributes(), true)
                );

                $this->logManager->channel('application')->error($exceptionMessage);

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            $this->sommelierRepo->setUnavailable($sommelierId);

            /**
             * Check the availability of wines
             */
            $statusAvailability = $this->orderRepo->processAvailabilityOfWines();

            if (true !== $statusAvailability) {
                $exceptionMessage = sprintf(
                    'Could not process availability of wines: %s',
                    print_r($this->orderRepo->order->getAttributes(), true)
                );

                $this->logManager->channel('application')->error($exceptionMessage);

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            $this->logManager->channel('application')->info(
                'The sommelier checks the availability of wines.',
                $this->orderRepo->order->getAttributes()
            );

            /**
             * Dispatch DeliverOrder Job
             */
            DeliverOrder::dispatch($this->orderRepo->order);

            $this->logManager->channel('application')->info('DeliverOrder Job was successfully queued.');

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

            $this->logManager->channel('application')->info(
                'The waiter delivers and closes the order.',
                $this->orderRepo->order->getAttributes()
            );

            $status = $this->orderRepo->deliverOrder();

            if (true !== $status) {
                $exceptionMessage = sprintf(
                    'Could not deliver and close the order: %s',
                    print_r($this->orderRepo->order->getAttributes(), true)
                );

                $this->logManager->channel('application')->error($exceptionMessage);

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
