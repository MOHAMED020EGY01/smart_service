<?php
namespace App\Events;

use App\Http\Resources\OrderRresource;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class OrderUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['user', 'provider']);
    }

    public function broadcastOn()
    {
        return [
            new Channel('user.' . $this->order->user_id),
            new Channel('provider.' . $this->order->provider_id),
        ];
    }

    public function broadcastAs()
    {
        return 'order.updated';
    }
    public function broadcastWith()
    {
        return [
            'order' => new OrderRresource($this->order)
        ];
    }
}
