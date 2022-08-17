<?php

namespace App\Jobs;

use App\Enums\NumberStatus;
use App\Enums\OrderStatus;
use App\Events\PaymentInformation;
use App\Events\PaymentManual;
use App\Models\Contest;
use App\Models\Order;
use App\Models\Sale;
use App\Models\User;
use App\Traits\MercadoPagoHelper;
use App\Traits\WhatsApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Traversable;

class JobReserveNumbers implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MercadoPagoHelper, WhatsApp;

    /** @var int */
    protected $random;

    /** @var array */
    protected $numbers;

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
    public function __construct(int $random = 0, array $numbers = [], Contest $contest, Order $order, User $customer)
    {
        $this->random = $random;
        $this->numbers = $numbers;
        $this->contest = $contest->withoutRelations();
        $this->order = $order->withoutRelations();
        $this->customer = $customer->withoutRelations();
    }

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 120;

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
        DB::transaction(function () {
            $is_random = $this->random > 0;
            $contest_numbers = $this->getContestNumbers($this->contest, $is_random);
            $reserved_numbers = [];
            $updated_numbers = [];
            $max_reserve_numbers = 0;

            $mem_usage =  round(memory_get_usage() / 1024 / 1024);
            logger("Pedido #{$this->order->id} - Consumo atual de memória: {$mem_usage}M");

            foreach ($contest_numbers as $number) {
                $number_exists = $is_random ? $max_reserve_numbers < $this->random : in_array($number->number, $this->numbers);
                $is_free = $number->status == NumberStatus::FREE;

                $can_reserve_number = $number_exists && $is_free;

                if ($can_reserve_number) {
                    $number->status = NumberStatus::RESERVED;
                    $number->order_id = $this->order->id;
                    $number->reserved_at = Carbon::now();
                    $number->customer->id = $this->customer->id;
                    $number->customer->name = $this->customer->name;
                    $number->customer->whatsapp = $this->customer->whatsapp;

                    if ($max_reserve_numbers < $this->random) {
                        $reserved_numbers[] = $number->number;
                        $max_reserve_numbers++;
                    }
                }

                $updated_numbers[] = json_encode($number);
            }

            $numbers = json_encode($updated_numbers);
            $order_total = $this->calcSaleDiscount(count($reserved_numbers), $this->contest);

            $this->order->total = $order_total;
            $this->order->status = OrderStatus::PENDING;
            $this->order->numbers = json_encode($reserved_numbers);
            $this->order->update();

            $this->contest->numbers = $numbers;
            $this->contest->update();

            $numbers = null;

            $payment = $this->createPayment($this->order, $this->customer, $this->contest);

            if ($payment == false) {
                logger("Pedido #{$this->order->id} - Pagamento manual");
                event(new PaymentManual($this->customer->id, $this->order->id));
            } else {
                logger("Pedido #{$this->order->id} - Pagamento automático");
                if (env('APP_ENV') != 'local') {
                    $this->sendReservationMessage($this->customer, $this->contest, $this->order, $payment);
                }

                $this->order->transaction_code = $payment['payment_id'];
                $this->order->update();

                event(new PaymentInformation($this->customer->id, $this->order->id, $payment));

                $peak_usage = round(memory_get_peak_usage() / 1024 / 1024);
                logger("Pedido #{$this->order->id} - Consumo máximo de memória: {$peak_usage}M");
            }
        });
    }

    /**
     * Calcula o valor total com base na promoção correspondente do pedido.
     * 
     * @param int $quantity
     * @param Contest $contest
     * 
     * @return float $partial
     */
    private function calcSaleDiscount(int $quantity, Contest $contest)
    {
        $partial = 0;
        $length = $quantity;
        $sales = Sale::where('contest_id', $contest->id)->get();
        $current_sale = Arr::first($sales, function ($value) use ($length) {
            return $length >= $value->quantity;
        });

        for ($i = 0; $i < $length; $i++) {
            if (!empty($current_sale)) {
                $partial += $current_sale->price;
            } else {
                $partial += $contest->price;
            }
        }

        return $partial;
    }

    /**
     * Parse JSON contest numbers.
     * 
     * @param Contest $contest
     * @param bool $random
     * 
     * @return Traversable
     */
    private function getContestNumbers(Contest $contest, bool $random = false): Traversable
    {
        $numbers = json_decode($contest->numbers);

        if ($random) {
            shuffle($numbers);
        }

        foreach ($numbers as $number) {
            yield json_decode($number);
        }
    }
}
