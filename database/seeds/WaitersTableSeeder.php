<?php

use App\Models\Waiter;
use App\Repositories\WaiterRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;

class WaitersTableSeeder extends Seeder
{
    /**
     * @var WaiterRepository
     */
    public $waiterRepo;

    /**
     * @var DatabaseManager
     */
    protected $db;

    public function __construct(Waiter $waiter, DatabaseManager $db)
    {
        $this->waiterRepo = new WaiterRepository($waiter);
        $this->db         = $db;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        try {

            $this->db->beginTransaction();

            $this->waiterRepo->put([
                'first_name' => 'Richard',
                'last_name'  => 'Goodman',
                'available'  => true
            ]);

            $this->waiterRepo->put([
                'first_name' => 'Paul',
                'last_name'  => 'Priestly',
                'available'  => true
            ]);

            $this->db->commit();

        } catch (Exception $e) {

            $this->db->rollBack();

            throw $e;

        }
    }
}
