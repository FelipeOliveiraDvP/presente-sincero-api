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

    if ($user_role->indentifier == 'admin') {
      return $this->sellerAbilities();
    }

    return $this->simpleCustomerAbilities();
  }

  /**
   * Get the "Vendedor" role uuid.
   * 
   * @return string
   */
  protected function getSellerRole()
  {
    $role = Role::where('identifier', '=', 'seller')->first();

    return $role->id;
  }

  /**
   * Get the "Cliente" role uuid.
   * 
   * @return string
   */
  protected function getCustomerRole()
  {
    $role = Role::where('identifier', '=', 'customer')->first();

    return $role->id;
  }
}
