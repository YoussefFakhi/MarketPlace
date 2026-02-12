@component('mail::message')
# Order Reminder: Action Required

Hello {{ $order->freelancer->name }},

This is a friendly reminder about order #{{ $order->id }} that is still pending your action.

**Order Details:**
- Order ID: {{ $order->id }}
- Service: {{ $order->service?->title ?: 'Service Deleted' }}
- Client: {{ $order->client->name }}
- Amount: ${{ $order->montant }}
- Status: {{ $order->status }}
- Created: {{ $order->created_at->format('Y-m-d H:i') }}

Please review this order and update the status when possible.

@component('mail::button', ['url' => config('app.url') . '/orders/' . $order->id])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
