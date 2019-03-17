<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrder;
use App\Repositories\OrderRepository;
use App\Services\WineSpectatorService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * @var OrderRepository
     */
    protected $orderRepo;

    public function __construct(DatabaseManager $db, OrderRepository $orderRepo)
    {
        $this->db        = $db;
        $this->orderRepo = $orderRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Log::channel('application')->info(
            'The customer enters in the order list page.',
            [auth()->user()->getAuthIdentifierName() => auth()->user()->getAuthIdentifier()]
        );

        $orders = $this->orderRepo->getAll();

        return view('order.index', ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param WineSpectatorService $wineSpectatorService
     * @return \Illuminate\Http\Response
     */
    public function create(WineSpectatorService $wineSpectatorService)
    {
        Log::channel('application')->info(
            'The customer enters in the create order page.',
            [auth()->user()->getAuthIdentifierName() => auth()->user()->getAuthIdentifier()]
        );

        $wines   = $wineSpectatorService->getAll();

        return view('order.create', ['wines' => $wines]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param OrderRepository $orderRepository
     * @return bool
     * @throws \Exception
     */
    public function store(Request $request, OrderRepository $orderRepository)
    {
        try {

            $this->db->beginTransaction();

            $order = $request->all();

            unset($order['_token']);

            $status = $orderRepository->put($order);

            if (true !== $status) {
                $exceptionMessage = sprintf(
                    'Could not save the order: %s',
                    print_r($order, true)
                );

                Log::channel('application')->error(
                    $exceptionMessage,
                    $orderRepository->order->getAttributes()
                );

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            ProcessOrder::dispatch($orderRepository->order);

            Log::channel('application')->info(
                'The customer submited an order to store',
                [
                    'order' => $orderRepository->order->getAttributes(),
                    'wines' => $orderRepository->wineOrder->getAttributes()
                ]
            );

            Log::channel('application')->info('ProcessOrder Job was successfully queued.');

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollBack();

            throw $e;

        }

        $successfullyMessage = 'Order was successfully placed.';

        Log::channel('application')->info(
            $successfullyMessage,
            [$orderRepository->order->getAttribute('id')]
        );

        \Session::flash('status', $successfullyMessage);

        return redirect()->route('orders.index');
    }
}
