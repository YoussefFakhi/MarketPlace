@component('mail::message')
# Order Status Updated

Hello {{ $order->client->name }},

Your order status has been updated from "{{ $oldStatus }}" to "{{ $newStatus }}".

**Order Details:**
- Order ID: {{ $order->id }}
- Service: {{ $order->service->title }}
- Freelancer: {{ $order->freelancer->name }}
- Amount: ${{ $order->service->price }}
- Current Status: {{ $newStatus }}

{{-- @component('mail::button', ['url' => config('app.url') . '/orders/' . $order->id])
View Order
@endcomponent --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
