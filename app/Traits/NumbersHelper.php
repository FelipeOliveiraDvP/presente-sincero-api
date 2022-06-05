<?php

namespace App\Traits;

use App\Enums\NumberStatus;
use App\Models\Contest;
use App\Models\User;
use Illuminate\Support\Carbon;

trait NumbersHelper
{
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
   * Set the contest numbers as FREE.
   * 
   * @param int $contest_id
   * @param array $numbers      
   * 
   * @return string|boolean
   */
  protected function setContestNumbersAsFree(int $contest_id, array $numbers)
  {
    $contest_numbers = $this->getContestNumbers($contest_id);
    $updated_numbers = [];

    foreach ($contest_numbers as $number) {
      if (in_array($number->number, $numbers) && $number->status == NumberStatus::RESERVED) {
        $number->status = NumberStatus::FREE;
        $number->reserved_at = null;
        $number->payment_at = null;
        $number->customer->id = null;
        $number->customer->name = null;
        $number->customer->whatsapp = null;
      }
      $updated_numbers[] = json_encode($number);
    }

    return json_encode($updated_numbers);
  }

  /**
   * Set the contest numbers as RESERVED.
   * 
   * @param int $contest_id
   * @param array $numbers   
   * @param User $customer
   * 
   * @return string|boolean
   */
  protected function setContestNumbersAsReserved(int $contest_id, array $numbers, User $customer)
  {
    $contest_numbers = $this->getContestNumbers($contest_id);
    $updated_numbers = [];

    foreach ($contest_numbers as $number) {
      if (in_array($number->number, $numbers) && $number->status == NumberStatus::FREE) {
        $number->status = NumberStatus::RESERVED;
        $number->reserved_at = Carbon::now();
        $number->customer->id = $customer->id;
        $number->customer->name = $customer->name;
        $number->customer->whatsapp = $customer->whatsapp;
      }
      $updated_numbers[] = json_encode($number);
    }

    return json_encode($updated_numbers);
  }

  /**
   * Set the contest numbers as PAID.
   * 
   * @param int $contest_id
   * @param array $numbers   
   * @param User $customer
   * 
   * @return string|boolean
   */
  protected function setContestNumbersAsPaid(int $contest_id, array $numbers, User $customer)
  {
    $contest_numbers = $this->getContestNumbers($contest_id);
    $updated_numbers = [];

    foreach ($contest_numbers as $number) {
      if (in_array($number->number, $numbers) && $number->status == NumberStatus::RESERVED) {
        $number->status = NumberStatus::PAID;
        $number->reserved_at = null;
        $number->payment_at = Carbon::now();
        $number->customer->id = $customer->id;
        $number->customer->name = $customer->name;
        $number->customer->whatsapp = $customer->whatsapp;
      }
      $updated_numbers[] = json_encode($number);
    }

    return json_encode($updated_numbers);
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
   * Get contest number by customer.
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
  protected function getContestNumbers(int $contest_id)
  {
    $contest = Contest::find($contest_id);

    if (empty($contest)) return [];

    $numbers_json = json_decode($contest->numbers);
    $numbers_decoded = [];

    foreach ($numbers_json as $number) {
      $numbers_decoded[] = json_decode($number);
    }

    return $numbers_decoded;
  }
}
