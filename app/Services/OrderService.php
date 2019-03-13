<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use Illuminate\Database\DatabaseManager;

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

    public function __construct(DatabaseManager $db, OrderRepository $orderRepo) {
        $this->db        = $db;
        $this->orderRepo = $orderRepo;
    }

    public function processNextOrder()
    {
        $nextOrder = $this->orderRepo->getNextOrder();

        dd($nextOrder);
    }
}
