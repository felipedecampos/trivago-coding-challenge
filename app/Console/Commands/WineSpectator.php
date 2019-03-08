<?php

namespace App\Console\Commands;

use App\Services\WineSpectatorService;
use Illuminate\Console\Command;

class WineSpectator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wine-spectator:watch {day=today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the wines';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param WineSpectatorService $wine_spectator
     * @return void
     * @throws \Exception
     */
    public function handle(WineSpectatorService $wine_spectator)
    {
        $status = $wine_spectator->updateWines($this->argument('day'));

        echo $status === true
            ? "Wines were successfully updated.\n"
            : "$status \n";
    }
}
