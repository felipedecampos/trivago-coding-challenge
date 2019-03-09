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
    protected $signature = 'wine-spectator:watch {date=today}';

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
     * @param WineSpectatorService $wineSpectator
     * @return bool
     * @throws \Exception
     */
    public function handle(WineSpectatorService $wineSpectator)
    {
        $date = null;
        if ($this->argument('date') !== 'all') {
            $date = new \DateTime($this->argument('date'), new \DateTimeZone('-05:00'));
        }

        $status = $wineSpectator->updateWines($date);

        if ($status === true) {
            $this->info('Wines were successfully updated.');

            return true;
        }

        $this->error('Could not save the wines.');

        return false;
    }
}
