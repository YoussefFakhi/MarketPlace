<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable; // Adds Queue settings like connection, queue name, or delay

use Illuminate\Foundation\Bus\Dispatchable; // Allows the Job to be sent to the Queue using ::dispatch()
use Illuminate\Queue\SerializesModels;//Safely stores Models (like Order or User) in the Queue for later use
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;//Lets you control the Job inside the Queue (delete, retry, fail)

class SendOrderConfirmationToClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client=$this->order->client;
        Mail::to($client->email)->send(new \App\Mail\OrderConfirmationMail($this->order));
    }
}
