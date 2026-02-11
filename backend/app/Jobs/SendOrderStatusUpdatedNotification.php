<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Mail;

class SendOrderStatusUpdatedNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order, public $oldStatus, public $newStatus)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client= $this->order->client;
        Mail::to($client->email)->send(mailable: new \App\Mail\OrderStatusUpdatedMail($this->order, $this->oldStatus, $this->newStatus));
    }
}
