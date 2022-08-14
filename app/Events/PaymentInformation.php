<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentInformation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var int */
    public $user_id;

    /** @var int */
    public $order_id;

    /** @var bool */
    public $mercado_pago;

    /** @var array */
    public $payment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $user_id, int $order_id, array $payment)
    {
        $this->user_id = $user_id;
        $this->order_id = $order_id;
        $this->mercado_pago = true;
        $this->payment = $payment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('payment.information');
    }
}
