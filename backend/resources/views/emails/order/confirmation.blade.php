@component('mail::message')
# Order Confirmation

Hello {{ $order->client->name }},

Your order has been successfully placed!

**Order Details:**
- Order ID: {{ $order->id }}
- Service: {{ $order->service->title }}
- Freelancer: {{ $order->freelancer->name }}
- Amount: ${{ $order->service->price }}
- Status: {{ $order->status }}

{{-- @component('mail::button', ['url' => config('app.url') . '/orders/' . $order->id])
View Order
@endcomponent --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
