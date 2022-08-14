<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentManual implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var int */
    public $user_id;

    /** @var int */
    public $order_id;

    /** @var bool */
    public $mercado_pago;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $user_id, int $order_id)
    {
        $this->user_id = $user_id;
        $this->order_id = $order_id;
        $this->mercado_pago = false;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('payment.manual');
    }
}
