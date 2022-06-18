<?php

namespace App\Traits;

trait AbilitiesHelper
{
  /**
   * Returns the simple login customer token abilities.
   * 
   * @return array<string>
   */
  protected function simpleCustomerAbilities()
  {
    return [
      'reserve.numbers',
      'free.numbers',
    ];
  }

  /**
   * Returns the full customer token abilities.
   * 
   * @return array<string>
   */
  protected function fullCustomerAbilities()
  {
    return [
      'reserve.numbers',
      'free.numbers',
      'view.profile',
      'edit.profile',
    ];
  }

  /**
   * Returns the seller token abilities.
   * 
   * @return array<string>
   */
  protected function sellerAbilities()
  {
    return [
      'reserve.numbers',
      'free.numbers',
      'view.profile',
      'edit.profile',
      'create.contests',
      'edit.contests',
      'manage.contests',
      'list.bank_accounts',
      'create.bank_accounts',
      'edit.bank_accounts',
      'remove.bank_accounts',
      'upload.images'
    ];
  }

  /**
   * Returns the admin token abilities.
   * 
   * @return array<string>
   */
  protected function adminAbilities()
  {
    return [
      'reserve.numbers',
      'free.numbers',
      'view.profile',
      'edit.profile',
      'create.contests',
      'edit.contests',
      'manage.contests',
      'list.bank_accounts',
      'create.bank_accounts',
      'edit.bank_accounts',
      'remove.bank_accounts',
      'upload.images'
    ];
  }
}
