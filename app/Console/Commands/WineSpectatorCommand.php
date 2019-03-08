<?php

namespace App\Console\Commands;

use App\Services\WineSpectatorService;
use Illuminate\Console\Command;

class WineSpectatorCommand extends Command
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
     * @return bool
     */
    public function handle(WineSpectatorService $wine_spectator)
    {
        $status = $wine_spectator->updateWines($this->argument('day'));

        if ($status === true) {
            $this->info('Wines were successfully updated.');

            return true;
        }

        $this->error($status);

        return false;
    }
}
