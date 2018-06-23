<?php

namespace App\Jobs;

use App\Http\Controllers\Api\MessageController;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NewOrderMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public $tries = 1;

    public function __construct(Order $order)
    {
        $this->order = $order; // 只序列化id
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = new MessageController();
        $message->newOrderMessage($this->order->id);
    }
}
