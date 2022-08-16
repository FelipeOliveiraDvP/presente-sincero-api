<?php

namespace App\Traits;

use App\Enums\NumberStatus;
use App\Enums\OrderStatus;
use App\Models\Contest;
use App\Models\Order;
use App\Models\User;
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

      $numbers[$i] = $number;
    }

    return json_encode($numbers->toArray());
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
   * Get contest numbers status.
   * 
   * @param int $contest_id   
   * 
   * @return array
   */
  protected function getContestNumbersStatus(int $contest_id)
  {
    $total = 0;
    $free = 0;
    $reserved = 0;
    $paid = 0;

    $contest_numbers = $this->getContestNumbers($contest_id);

    foreach ($contest_numbers as $value) {
      if ($value->status == NumberStatus::FREE) {
        $free++;
      }

      if ($value->status == NumberStatus::RESERVED) {
        $reserved++;
      }

      if ($value->status == NumberStatus::PAID) {
        $paid++;
      }

      $total++;
    }

    return [
      'total' => $total,
      'free' => $free,
      'reserved' => $reserved,
      'paid' => $paid,
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
   * @return Traversable
   */
  protected function getContestNumbers(int $contest_id, bool $random = false): Traversable
  {
    $contest = Contest::find($contest_id);

    if (empty($contest)) return [];

    $numbers = json_decode($contest->numbers);

    if ($random == true) {
      shuffle($numbers);
    }

    foreach ($numbers as $number) {
      yield json_decode($number);
    }
  }
}
