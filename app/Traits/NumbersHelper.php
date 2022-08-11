<?php

namespace App\Traits;

use App\Enums\NumberStatus;
use App\Enums\OrderStatus;
use App\Models\Contest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use SplFixedArray;
use Traversable;

trait NumbersHelper
{
  use AuthHelper;

  /**
   * Generate the contest numbers JSON.
   *      
   * @param int $quantity
   * 
   * @return string|bool
   */
  protected function generateContestNumbers(int $quantity)
  {
    $numbers = new SplFixedArray($quantity);

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

    return json_encode($numbers->toArray());
  }

  /**
   * Set the contest numbers as RESERVED.
   *
   * @param int $random
   * @param array<string> $numbers
   * @param Contest $contest
   * @param Order $order
   * @param User $customer   
   * 
   * @return array<string, string|bool>
   */
  protected function setContestNumbersAsReserved(int $random = 0, array $numbers = [], Contest $contest, Order $order, User $customer)
  {
    $is_random = $random > 0;
    $contest_numbers = $this->getContestNumbers($$contest->id, $is_random);
    $reserved_numbers = new SplFixedArray($is_random ? $random : count($numbers));
    $updated_numbers = new SplFixedArray($contest->quantity);
    $max_reserve_numbers = 0;

    for ($i = 0; $i < $contest->quantity; $i++) {
      $number_exists = $is_random ? $max_reserve_numbers < $random : in_array($contest_numbers[$i]->number, $numbers);
      $is_free = $contest_numbers[$i]->status == NumberStatus::FREE;

      $can_reserve_number = $number_exists && $is_free;

      if ($can_reserve_number) {
        $contest_numbers[$i]->status = NumberStatus::RESERVED;
        $contest_numbers[$i]->order_id = $order->id;
        $contest_numbers[$i]->reserved_at = Carbon::now();
        $contest_numbers[$i]->customer->id = $customer->id;
        $contest_numbers[$i]->customer->name = $customer->name;
        $contest_numbers[$i]->customer->whatsapp = $customer->whatsapp;

        if ($max_reserve_numbers < $random) {
          $reserved_numbers[$i] = $contest_numbers[$i]->number;
          $max_reserve_numbers++;
        }
      }

      $updated_numbers[$i] = json_encode($contest_numbers[$i]);
    }

    return [
      'reserved_numbers' => json_encode($reserved_numbers->toArray()),
      'updated_numbers'  => json_encode($updated_numbers->toArray()),
    ];
  }

  /**
   * Set the contest numbers as FREE.
   * 
   * @param Contest $contest
   * @param Order $order
   * @param User $customer   
   * 
   * @return array<string, string|bool>
   */
  protected function setContestNumbersAsFree(Contest $contest, Order $order, User $customer)
  {
    $contest_numbers = $this->getContestNumbers($contest->id);
    $order_numbers = json_decode($order->numbers);
    $free_numbers = new SplFixedArray(count(json_decode($order->numbers)));
    $updated_numbers = new SplFixedArray($contest->quantity);

    for ($i = 0; $i < $contest->quantity; $i++) {
      $number_exists = in_array($contest_numbers[$i]->number, $order_numbers);
      $is_reserved = $contest_numbers[$i]->status == NumberStatus::RESERVED;
      $is_owner = $customer->id == $contest_numbers[$i]->customer->id;
      $can_free_number = $number_exists && $is_reserved && $is_owner;

      if ($can_free_number) {
        $contest_numbers[$i]->status = NumberStatus::FREE;
        $contest_numbers[$i]->order_id = null;
        $contest_numbers[$i]->reserved_at = null;
        $contest_numbers[$i]->payment_at = null;
        $contest_numbers[$i]->customer->id = null;
        $contest_numbers[$i]->customer->name = null;
        $contest_numbers[$i]->customer->whatsapp = null;

        $free_numbers[$i] = $contest_numbers[$i]->number;
      }

      $updated_numbers[$i] = json_encode($contest_numbers[$i]);
    }

    return [
      'free_numbers'    => json_encode($free_numbers->toArray()),
      'updated_numbers' => json_encode($updated_numbers->toArray())
    ];
  }

  /**
   * Set the contest numbers as PAID.
   * 
   * @param Contest $contest
   * @param Order $order   
   * @param User $customer
   * 
   * @return array<string, string|bool>
   */
  protected function setContestNumbersAsPaid(Contest $contest, Order $order, User $customer)
  {
    $contest_numbers = $this->getContestNumbers($contest->id);
    $order_numbers = json_decode($order->numbers);
    $paid_numbers = new SplFixedArray(count(json_decode($order->numbers)));
    $updated_numbers = new SplFixedArray($contest->quantity);

    for ($i = 0; $i < $contest->quantity; $i++) {
      $number_exists = in_array($contest_numbers[$i]->number, $order_numbers);
      $is_reserved = $contest_numbers[$i]->status == NumberStatus::RESERVED;
      $is_owner = $customer->id == $contest_numbers[$i]->customer->id;

      $can_pay_number = $number_exists && $is_reserved && $is_owner;

      if ($can_pay_number) {
        $contest_numbers[$i]->status = NumberStatus::PAID;
        $contest_numbers[$i]->payment_at = Carbon::now();
        $contest_numbers[$i]->customer->id = $customer->id;
        $contest_numbers[$i]->customer->name = $customer->name;
        $contest_numbers[$i]->customer->whatsapp = $customer->whatsapp;

        $paid_numbers[$i] = $contest_numbers[$i]->number;
      }

      $updated_numbers[$i] = json_encode($contest_numbers[$i]);
    }

    return [
      'paid_numbers'    => json_encode($paid_numbers->toArray()),
      'updated_numbers' => json_encode($updated_numbers->toArray()),
    ];
  }

  /**
   * Count total numbers by status.
   * 
   * @param int $contest_id
   * @param OrderStatus $status
   * 
   * @return int
   */
  protected function countNumbersByStatus(int $contest_id, string $status)
  {
    $total = 0;
    $contest_numbers = $this->getContestNumbers($contest_id);

    foreach ($contest_numbers as $value) {
      if ($value->status == $status) {
        $total++;
      }
    }

    return $total;
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
  protected function getContestNumbersByNumbers(int $contest_id, array $numbers): Traversable
  {
    $contest_numbers = $this->getContestNumbers($contest_id);

    foreach ($contest_numbers as $number) {
      $number_exists = in_array($number->number, $numbers);

      if ($number_exists) {
        yield $number;
      }
    }
  }

  /**
   * Get contest numbers by customer.
   * 
   * @param int $contest_id
   * @param User $customer
   * 
   * @return array
   */
  protected function getContestNumbersByCustomer(int $contest_id, User $customer): Traversable
  {
    $numbers = $this->getContestNumbers($contest_id);

    foreach ($numbers as $value) {
      if ($value->customer->id == $customer->id) {
        yield $value;
      }
    }
  }

  /**
   * Get contest numbers by order.
   * 
   * @param int $contest_id
   * @param Order $order
   * 
   * @return array
   */
  protected function getContestNumbersByOrder(int $contest_id, Order $order): Traversable
  {
    $numbers = $this->getContestNumbers($contest_id);

    foreach ($numbers as $value) {
      if ($value->order_id == $order->id) {
        yield $value;
      }
    }
  }

  /**
   * Get contest numbers by status.
   * 
   * @param int $contest_id
   * @param string $status
   * 
   * @return Traversable
   */
  protected function getContestNumbersByStatus(int $contest_id, string $status): Traversable
  {
    $numbers = $this->getContestNumbers($contest_id);

    foreach ($numbers as $value) {
      if ($value->status == $status) {
        yield $value;
      }
    }
  }

  /**
   * Get the contest numbers as array.
   * 
   * @param int $contest_id
   * 
   * @return array
   */
  protected function getContestNumbers(int $contest_id, bool $random = false): Traversable
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
  }
}
