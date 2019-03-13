<?php

namespace App\Jobs;

use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var OrderService
     */
    public $orderService;

    /**
     * Create a new job instance.
     *
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Execute the job.
     *
     * @param OrderService $orderService
     * @return void
     */
    public function handle(OrderService $orderService)
    {
        $orderService->processNextOrder();
    }

    /**
     * The job failed to process.
     *
     * @param \Exception $e
     * @return void
     */
    public function failed(\Exception $e)
    {
        LOG::error('Error (' . $e->getCode() . '): ' . $e->getMessage(), $e->getTrace());
    }
}
