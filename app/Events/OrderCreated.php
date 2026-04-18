<?php
namespace App\Events;

use App\Http\Resources\OrderRresource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new PrivateChannel($this->order);
    }

    public function broadcastAs()
    {
        return 'order.created';
    }
    public function broadcastWith()
    {
        return [
            'order' => new OrderRresource($this->order)
        ];
    }
}
