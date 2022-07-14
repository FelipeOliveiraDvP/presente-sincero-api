<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\User;

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
   * Get the "Admin" role UUID.
   * 
   * @return string
   */
  protected function getAdminRole()
  {
    $role = Role::where('identifier', '=', 'admin')->first();

    return $role->id;
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

  /**
   * Verifiy if a user is an "Admin"
   * 
   * @return boolean
   */
  protected function isAdmin(User $user)
  {
    return $user->role == $this->getAdminRole();
  }

  /**
   * Verifiy if a user is an "Seller"
   * 
   * @return boolean
   */
  protected function isSeller(User $user)
  {
    return $user->role == $this->getSellerRole();
  }

  /**
   * Verifiy if a user is an "Customer"
   * 
   * @return boolean
   */
  protected function isCustomer(User $user)
  {
    return $user->role == $this->getCustomerRole();
  }
}
