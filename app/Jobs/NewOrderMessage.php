<?php

namespace App\Jobs;

use App\Http\Controllers\Api\MessageController;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NewOrderMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $order;
    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public $tries = 3;

    public function __construct(MessageController $message, $order)
    {
        $this->message = $message;
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->message->newOrderMessage($this->order);
    }
}
