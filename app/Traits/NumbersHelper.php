<?php

namespace App\Traits;

use App\Enums\NumberStatus;
use App\Models\Contest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;

trait NumbersHelper
{
  use AuthHelper;

  /**
   * Generate the contest numbers JSON.
   *      
   * @param int $quantity
   * 
   * @return string|boolean
   */
  protected function generateContestNumbers(int $quantity)
  {
    $numbers = [];

    for ($i = 0; $i < $quantity; $i++) {
      $number_length = strlen((string) $quantity);

      $number = json_encode([
        'number'      => str_pad($i, $number_length, '0', STR_PAD_LEFT),
        'status'      => NumberStatus::FREE,
        'order_id'    => null,
        'reserved_at' => null,
        'payment_at'  => null,
        'customer'  => [
          'id'       => null,
          'name'     => null,
          'whatsapp' => null
        ],
      ]);

      $numbers[] = $number;
    }

    return json_encode($numbers);
  }

  /**
   * Set the contest numbers as RESERVED.
   * 
   * @param int $contest_id
   * @param int $random
   * @param array<string> $numbers
   * @param Order $order
   * @param User $customer   
   * 
   * @return array
   */
  protected function setContestNumbersAsReserved(int $contest_id, int $random = 0, array $numbers = [], Order $order, User $customer)
  {
    $contest_numbers = $this->getContestNumbers($contest_id, $random > 0);
    $reserved_numbers = [];
    $updated_numbers = [];
    $max_reserve_numbers = 0;

    // if ($random > 0) {
    //   shuffle($contest_numbers);
    // }

    foreach ($contest_numbers as $number) {
      $is_random = $random > 0;
      $number_exists = $is_random ? $max_reserve_numbers < $random : in_array($number->number, $numbers);
      $is_free = $number->status == NumberStatus::FREE;

      $can_reserve_number = $number_exists && $is_free;

      if ($can_reserve_number) {
        $number->status = NumberStatus::RESERVED;
        $number->order_id = $order->id;
        $number->reserved_at = Carbon::now();
        $number->customer->id = $customer->id;
        $number->customer->name = $customer->name;
        $number->customer->whatsapp = $customer->whatsapp;

        if ($max_reserve_numbers < $random) {
          $reserved_numbers[] = $number->number;
          $max_reserve_numbers++;
        }
      }

      $updated_numbers[] = json_encode($number);
    }

    return [
      'reserved_numbers' => $reserved_numbers,
      'updated_numbers'  => $updated_numbers,
    ];
  }

  /**
   * Set the contest numbers as FREE.
   * 
   * @param int $contest_id
   * @param Order $order
   * @param User $customer   
   * 
   * @return array<string, array>
   */
  protected function setContestNumbersAsFree(int $contest_id, Order $order, User $customer)
  {
    // $contest = Contest::find($contest_id);
    $contest_numbers = $this->getContestNumbers($contest_id);
    $order_numbers = json_decode($order->numbers);
    $free_numbers = [];
    $updated_numbers = [];

    foreach ($contest_numbers as $number) {
      $number_exists = in_array($number->number, $order_numbers);
      $is_reserved = $number->status == NumberStatus::RESERVED;
      // $out_reserve_interval = $is_reserved ? Carbon::make($number->reserved_at)->diff(Carbon::now())->d > $contest->max_reserve_days : false;
      $is_owner = $customer->id == $number->customer->id;
      $can_free_number = $number_exists && $is_reserved && $is_owner;

      if ($can_free_number) {
        $number->status = NumberStatus::FREE;
        $number->order_id = null;
        $number->reserved_at = null;
        $number->payment_at = null;
        $number->customer->id = null;
        $number->customer->name = null;
        $number->customer->whatsapp = null;

        $free_numbers[] = $number->number;
      }

      $updated_numbers[] = json_encode($number);
    }

    return [
      'free_numbers'    => $free_numbers,
      'updated_numbers' => json_encode($updated_numbers)
    ];
  }

  /**
   * Set the contest numbers as PAID.
   * 
   * @param int $contest_id
   * @param Order $order   
   * @param User $customer
   * 
   * @return array
   */
  protected function setContestNumbersAsPaid(int $contest_id, Order $order, User $customer)
  {
    $contest_numbers = $this->getContestNumbers($contest_id);
    $order_numbers = json_decode($order->numbers);
    $paid_numbers = [];
    $updated_numbers = [];

    foreach ($contest_numbers as $number) {
      $number_exists = in_array($number->number, $order_numbers);
      $is_reserved = $number->status == NumberStatus::RESERVED;
      $is_owner = $customer->id == $number->customer->id;

      $can_pay_number = $number_exists && $is_reserved && $is_owner;

      if ($can_pay_number) {
        $number->status = NumberStatus::PAID;
        $number->payment_at = Carbon::now();
        $number->customer->id = $customer->id;
        $number->customer->name = $customer->name;
        $number->customer->whatsapp = $customer->whatsapp;

        $paid_numbers[] = $number->number;
      }

      $updated_numbers[] = json_encode($number);
    }

    return [
      'paid_numbers'    => $paid_numbers,
      'updated_numbers' => json_encode($updated_numbers),
    ];
  }

  /**
   * Get contest number by number.
   * 
   * @param int $contest_id
   * @param string $number
   * 
   * @return object|null
   */
  protected function getContestNumberByNumber(int $contest_id, string $number)
  {
    $numbers = $this->getContestNumbers($contest_id);

    foreach ($numbers as $value) {
      if ($value->number == $number) {
        return $value;
      }
    }

    return null;
  }

  /**
   * Get contest numbers by numbers.
   * 
   * @param int $contest_id
   * @param array<int> $numbers
   * 
   * @return array
   */
  protected function getContestNumbersByNumbers(int $contest_id, array $numbers)
  {
    $contest_numbers = $this->getContestNumbers($contest_id);
    $filtered_numbers = [];

    foreach ($contest_numbers as $number) {
      $number_exists = in_array($number->number, $numbers);

      if ($number_exists) {
        $filtered_numbers[] = $number;
      }
    }

    return $filtered_numbers;
  }

  /**
   * Get contest numbers by customer.
   * 
   * @param int $contest_id
   * @param User $customer
   * 
   * @return array
   */
  protected function getContestNumbersByCustomer(int $contest_id, User $customer)
  {
    $numbers = $this->getContestNumbers($contest_id);
    $customer_numbers = [];

    foreach ($numbers as $value) {
      if ($value->customer->id == $customer->id) {
        $customer_numbers[] = $value;
      }
    }

    return $customer_numbers;
  }

  /**
   * Get contest numbers by order.
   * 
   * @param int $contest_id
   * @param Order $order
   * 
   * @return array
   */
  protected function getContestNumbersByOrder(int $contest_id, Order $order)
  {
    $numbers = $this->getContestNumbers($contest_id);
    $order_numbers = [];

    foreach ($numbers as $value) {
      if ($value->order_id == $order->id) {
        $order_numbers[] = $value;
      }
    }

    return $order_numbers;
  }

  /**
   * Get contest numbers by status.
   * 
   * @param int $contest_id
   * @param string $status
   * 
   * @return array
   */
  protected function getContestNumbersByStatus(int $contest_id, string $status)
  {
    $numbers = $this->getContestNumbers($contest_id);
    $filtered_numbers = [];

    foreach ($numbers as $value) {
      if ($value->status == $status) {
        $filtered_numbers[] = $value;
      }
    }

    return $filtered_numbers;
  }

  /**
   * Get the contest numbers as array.
   * 
   * @param int $contest_id
   * 
   * @return array
   */
  protected function getContestNumbers(int $contest_id, bool $random = false)
  {
    $contest = Contest::find($contest_id);

    if (empty($contest)) return [];

    $numbers_json = json_decode($contest->numbers);

    if ($random == true) {
      shuffle($numbers_json);
    }

    foreach ($numbers_json as $number) {
      yield json_decode($number);
    }

    // $numbers_decoded = [];

    // foreach ($numbers_json as $number) {
    //   $numbers_decoded[] = json_decode($number);
    // }

    // return $numbers_decoded;
  }
}
