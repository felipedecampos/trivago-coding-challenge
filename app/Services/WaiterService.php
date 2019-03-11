<?php

namespace App\Services;

use App\Repositories\WaiterRepository;
use Illuminate\Database\DatabaseManager;

class WaiterService
{
    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * @var WaiterRepository
     */
    public $waiterRepo;

    public function __construct(DatabaseManager $db, WaiterRepository $waiterRepo) {
        $this->waiterRepo = $waiterRepo;
        $this->db         = $db;
    }

    public function getAll()
    {
        return $this->waiterRepo->getAll();
    }

    public function getAllAvailable()
    {
        return $this->waiterRepo->getAllAvailable();
    }
}
