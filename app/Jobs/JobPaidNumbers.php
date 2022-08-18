<?php

namespace App\Jobs;

use App\Enums\NumberStatus;
use App\Enums\OrderStatus;
use App\Events\PaymentConfirmed;
use App\Models\Contest;
use App\Models\FailedOrders;
use App\Models\Order;
use App\Models\User;
use App\Traits\WhatsApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Traversable;

class JobPaidNumbers implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WhatsApp;

    /** @var Contest */
    protected $contest;

    /** @var Order */
    protected $order;

    /** @var User */
    protected $customer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Contest $contest, Order $order, User $customer)
    {
        $this->contest = $contest->withoutRelations();
        $this->order = $order->withoutRelations();
        $this->customer = $customer->withoutRelations();
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
        $paid_numbers = [];
        $updated_numbers = [];

        $this->order->update(['status' => OrderStatus::PROCESSING]);

        foreach ($contest_numbers as $number) {
            $number_exist = in_array($number->number, $order_numbers);
            $is_reserved = $number->status == NumberStatus::RESERVED;
            $is_owner = $number->customer->id === $this->customer->id;

            $can_paid_number = $number_exist && $is_reserved && $is_owner;

            if ($can_paid_number) {
                $number->status = NumberStatus::PAID;
                $number->payment_at = Carbon::now();
                $number->customer->id = $this->customer->id;
                $number->customer->name = $this->customer->name;
                $number->customer->whatsapp = $this->customer->whatsapp;

                $paid_numbers[] = $number->number;
            }

            $updated_numbers[] = json_encode($number);
        }

        DB::transaction(function () use ($updated_numbers, $paid_numbers) {
            $paid_percentage = count($paid_numbers) / $this->contest->quantity;

            $this->contest->numbers = json_encode($updated_numbers);
            $this->contest->paid_percentage += round($paid_percentage, 2);
            $this->contest->update();

            $this->order->status = OrderStatus::CONFIRMED;
            $this->order->confirmed_at = Carbon::now();
            $this->order->update();

            if (env('APP_ENV') != 'local') {
                $this->sendConfirmationMessage($this->customer, $this->contest, $this->order);
            }

            logger("Pedido #{$this->order->id} - PAGO \nNome: {$this->customer->name}\nWhatsApp: {$this->customer->whatsapp}\nNúmeros: [{$this->order->numbers}]\n");
            event(new PaymentConfirmed($this->customer->id, $this->order->id));
        });
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        logger("Pedido #{$this->order->id} - Erro ao confirmar o pagamento");

        FailedOrders::create([
            'order_id' => $this->order->id,
            'customer_id' => $this->customer->id,
            'contest_id' => $this->contest->id,
            'cause' => $exception->getMessage(),
            'current_order_status' => $this->order->status,
            'next_order_status' => OrderStatus::CONFIRMED,
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
