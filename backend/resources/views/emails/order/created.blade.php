@component('mail::message')
# New Order Received

Hello {{ $order->freelancer->name }},

You have received a new order from {{ $order->client->name }}.

**Order Details:**
- Order ID: {{ $order->id }}
- Service: {{ $order->service->title }}
- Client: {{ $order->client->name }}
- Amount: ${{ $order->service->price }}
- Status: {{ $order->status }}

{{-- @component('mail::button', ['url' => config('app.url') . '/orders/' . $order->id])
View Order
@endcomponent --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
