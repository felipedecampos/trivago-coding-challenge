<?php

namespace App\Jobs;

use App\Models\Order;
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
     * @var Order
     */
    protected $order;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @param OrderService $orderService
     * @return void
     * @throws \Exception
     */
    public function handle(OrderService $orderService)
    {
        $orderService->processNextOrder($this->order);
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