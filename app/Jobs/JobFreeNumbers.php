<?php

namespace App\Jobs;

use App\Enums\NumberStatus;
use App\Enums\OrderStatus;
use App\Models\Contest;
use App\Models\FailedOrders;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Traversable;

class JobFreeNumbers implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Contest */
    protected $contest;

    /** @var Order */
    protected $order;

    /** @var User */
    protected $customer;

    /** @var bool */
    protected $cancel_order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Contest $contest, Order $order, User $customer, bool $cancel_order = false)
    {
        $this->contest = $contest->withoutRelations();
        $this->order = $order->withoutRelations();
        $this->customer = $customer->withoutRelations();
        $this->cancel_order = $cancel_order;
    }

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 60;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->contest->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contest_numbers = $this->getContestNumbers($this->contest);
        $order_numbers = json_decode($this->order->numbers);
        $updated_numbers = [];

        $this->order->update(['status' => OrderStatus::PROCESSING]);

        logger("Pedido #{$this->order->id} - Liberando números");

        foreach ($contest_numbers as $number) {
            $number_exist = in_array($number->number, $order_numbers);
            $is_reserved = $number->status == NumberStatus::RESERVED;
            $is_owner = $number->customer->id === $this->customer->id;

            $can_free_number = $number_exist && $is_reserved && $is_owner;

            if ($can_free_number) {
                $number->status = NumberStatus::FREE;
                $number->order_id = null;
                $number->reserved_at = null;
                $number->customer->id = null;
                $number->customer->name = null;
                $number->customer->whatsapp = null;
            }

            $updated_numbers[] = json_encode($number);
        }


        $this->contest->numbers = json_encode($updated_numbers);
        $this->contest->update();

        if ($this->cancel_order == true) {
            $this->order->status = OrderStatus::CANCELED;
            $this->order->update();
        } else {
            $this->order->delete();
        }

        logger("Pedido #{$this->order->id} - Números liberados");
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        logger("Pedido #{$this->order->id} - Erro ao liberar os números");

        FailedOrders::create([
            'order_id' => $this->order->id,
            'customer_id' => $this->customer->id,
            'contest_id' => $this->contest->id,
            'cause' => $exception->getMessage(),
            'current_order_status' => $this->order->status,
            'next_order_status' => OrderStatus::CANCELED,
        ]);
    }

    /**
     * Parse JSON contest numbers.
     * 
     * @param Contest $contest
     * @return Traversable
     */
    private function getContestNumbers(Contest $contest): Traversable
    {
        $numbers = json_decode($contest->numbers);

        foreach ($numbers as $number) {
            yield json_decode($number);
        }
    }
}
