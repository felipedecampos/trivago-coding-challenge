<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use App\Services\WineSpectatorService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @var OrderRepository
     */
    public $orderRepo;

    public function __construct(OrderRepository $orderRepo)
    {
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
     * @return \Illuminate\Http\Response
     */
    public function create(WineSpectatorService $wineSpectatorService)
    {
        $wines = $wineSpectatorService->getAll();

//        echo "<pre>";
//        print_r($wines);
//        die('died');

        $waiters = [
            ['first_name' => 'Richard', 'last_name'  => 'Goodman', 'available'  => false],
            ['first_name' => 'Paul', 'last_name'  => 'Priestly', 'available'  => false]
        ];

        return view('order.create', ['orders' => $waiters, 'wines' => $wines]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
