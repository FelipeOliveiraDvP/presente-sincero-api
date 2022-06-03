<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Http;

trait WhatsApp
{
  /**
   * Send a WhatsApp message.
   * 
   * @param User $user
   * @param string $message
   * 
   * @return boolean
   */
  protected function sendMessage(User $user, string $message)
  {
    $api = 'https://v4.chatpro.com.br/chatpro-qygz3swuoo/api/v1/send_message';
    $headers = [
      'Authorization' =>  '0w6y11mxbqavng3fuwnvsb6t0f19ts',
      'Content-Type' =>  'application/json'
    ];

    $response = Http::withHeaders($headers)
      ->post($api, [
        'message' => $message,
        'number' => $user->whatsapp
      ]);

    return $response->status() === 200;
  }

  /**
   * Send recovery password message.
   * 
   * @param User $user
   * 
   * @return boolean
   */
  protected function sendRecoveryMessage(User $user, string $code)
  {
    $message = "Olá *{$user->name}*, tudo bem?\n\nUtilize o código abaixo para recuperar sua senha no Presente Sincero.\n\n*{$code}*";

    return $this->sendMessage($user, $message);
  }
}
