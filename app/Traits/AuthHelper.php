<?php

namespace App\Traits;

use App\Models\Role;

trait AuthHelper
{
  use AbilitiesHelper;

  /**
   * Return the user abilities by role.
   * 
   * @param string $role
   * 
   * @return array
   */
  protected function getUserAbilities(string $role)
  {
    $user_role = Role::find($role);

    if ($user_role->identifier == 'admin') {
      return $this->adminAbilities();
    }

    if ($user_role->identifier == 'seller') {
      return $this->sellerAbilities();
    }

    if ($user_role->identifier == 'customer') {
      return $this->fullCustomerAbilities();
    }

    return $this->simpleCustomerAbilities();
  }

  /**
   * Get the "Seller" role UUID.
   * 
   * @return string
   */
  protected function getSellerRole()
  {
    $role = Role::where('identifier', '=', 'seller')->first();

    return $role->id;
  }

  /**
   * Get the "Customer" role UUID.
   * 
   * @return string
   */
  protected function getCustomerRole()
  {
    $role = Role::where('identifier', '=', 'customer')->first();

    return $role->id;
  }
}
