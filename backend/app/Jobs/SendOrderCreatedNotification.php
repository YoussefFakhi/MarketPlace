<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable; // Adds Queue settings like connection, queue name, or delay

use Illuminate\Foundation\Bus\Dispatchable; // Allows the Job to be sent to the Queue using ::dispatch()
use Illuminate\Queue\SerializesModels;//Safely stores Models (like Order or User) in the Queue for later use
use Illuminate\Queue\InteractsWithQueue;//Lets you control the Job inside the Queue (delete, retry, fail)
use Illuminate\Support\Facades\Mail;

class SendOrderCreatedNotification implements ShouldQueue   // implements ShouldQueue   : this will work in backeground
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
        //get the freelancer
        $freelancer= $this->order->freelancer;

        Mail::to($freelancer->email)->send(new \App\Mail\OrderCreatedMail($this->order));
    }
}




// [User Action]
//        ↓
// [Controller -> Create Order]
//        ↓
// [Dispatch Job to Queue]  <-- Controller returns Response
//        ↓
// [Queue Worker picks Job]
//        ↓
// [Job handle() runs]
//        ↓
// [Instantiate Mailable (OrderCreatedMail)]
//        ↓
// [build() -> markdown template + subject]
//        ↓
// [Mail::send() -> Email is sent]
//        ↓
// [Recipient receives Email]
