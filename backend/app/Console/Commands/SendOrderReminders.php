<?php

namespace App\Console\Commands;

use App\Jobs\SendOrderReminderNotification;
use App\Models\Order;
use Illuminate\Console\Command;

class SendOrderReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-order-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for pending orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find orders that are pending and older than 5 hours
        $pendingOrders = Order::where('status', 'pending')
            ->where('created_at','<',now()->subMinute())   //subHours(5))
            ->where(function($query){
                    $query->where('last_reminder_sent_at', '<', now()->subMinute())
                        ->orWhereNull('last_reminder_sent_at');
        })
        ->with(['freelancer', 'service'])
        ->get();

        //we use to count how much order we sent after runing this command
        $sentCount = 0;

        foreach($pendingOrders as $order){
            // Dispatch job to send reminder email
            SendOrderReminderNotification::dispatch($order);

            $order->update(['last_reminder_sent_at'=>now()]);

            $this->info("Reminder sent for order #{$order->id}");
            $sentCount++;
        }
        $this->info("Sent reminders for {$sentCount} pending orders");
    }
}
