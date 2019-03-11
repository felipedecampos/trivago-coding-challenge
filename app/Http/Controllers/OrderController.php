<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use App\Services\WaiterService;
use App\Services\WineSpectatorService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * @var OrderRepository
     */
    protected $orderRepo;

    /**
     * @var DatabaseManager
     */
    protected $db;

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
        $orders = $this->orderRepo->getAll();

        return view('order.index', ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param WineSpectatorService $wineSpectatorService
     * @param WaiterService $waiterService
     * @return \Illuminate\Http\Response
     */
    public function create(WineSpectatorService $wineSpectatorService, WaiterService $waiterService)
    {
        $wines   = $wineSpectatorService->getAll();
        $waiters = $waiterService->getAllAvailable();

        return view('order.create', ['orders' => $waiters, 'wines' => $wines]);
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

                throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollBack();

            throw $e;

        }

        \Session::flash('status','Order successfully placed.');

        return redirect()->route('orders.index');
    }
}
