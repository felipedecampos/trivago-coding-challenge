<?php

use App\Models\Waiter;
use App\Repositories\WaiterRepository;
use Illuminate\Database\Seeder;

class WaitersTableSeeder extends Seeder
{
    /**
     * @var WaiterRepository
     */
    public $waiterRepo;

    public function __construct(Waiter $waiter)
    {
        $this->waiterRepo = new WaiterRepository($waiter);
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

            $this->waiterRepo->put([
                'first_name' => 'Richard',
                'last_name'  => 'Goodman',
                'available'  => false
            ]);

            $this->waiterRepo->put([
                'first_name' => 'Paul',
                'last_name'  => 'Priestly',
                'available'  => false
            ]);

        } catch (Exception $e) {

            throw $e;

        }
    }
}
