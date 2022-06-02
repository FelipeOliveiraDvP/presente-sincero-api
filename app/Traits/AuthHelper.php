<?php

namespace App\Traits;

use App\Models\Role;

trait AuthHelper
{
  /**
   * Return the user abilities by role.
   * 
   * @param string $role
   * 
   * @return array
   */
  protected function getUserAbilities(string $role)
  {
    $userRole = Role::find($role);

    if ($userRole->name == 'Administrador') {
      return [
        'manage:contests',
        'view:contests',
        'manage:bank_accounts',
        'view:bank_accounts',
        'manage:upload',
      ];
    }

    return [];
  }

  /**
   * Get the "Vendedor" role uuid.
   * 
   * @return string
   */
  protected function getSellerRole()
  {
    $role = Role::where('name', '=', 'Vendedor')->first();

    return $role->id;
  }

  /**
   * Get the "Cliente" role uuid.
   * 
   * @return string
   */
  protected function getCustomerRole()
  {
    $role = Role::where('name', '=', 'Cliente')->first();

    return $role->id;
  }
}
