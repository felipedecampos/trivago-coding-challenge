<?php

use App\Models\Sommelier;
use App\Repositories\SommelierRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;

class SommeliersTableSeeder extends Seeder
{
    /**
     * @var SommelierRepository
     */
    public $sommelierRepo;

    /**
     * @var DatabaseManager
     */
    protected $db;

    public function __construct(Sommelier $sommelier, DatabaseManager $db)
    {
        $this->sommelierRepo = new SommelierRepository($sommelier);
        $this->db            = $db;
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

            $this->sommelierRepo->put([
                'first_name' => 'John',
                'last_name'  => 'Shepherd',
                'available'  => true
            ]);

            $this->db->commit();

        } catch (Exception $e) {

            $this->db->rollBack();

            throw $e;

        }
    }
}
