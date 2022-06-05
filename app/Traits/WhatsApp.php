<?php

namespace App\Traits;

use App\Models\Contest;
use App\Models\User;
use Illuminate\Support\Facades\Http;

trait WhatsApp
{

  /**
   * Send win contest message.
   * 
   * @param User $user
   * @param Contest $contest
   * 
   * @return boolean
   */
  protected function sendWinContestMessage(User $user, Contest $contest)
  {
    $message = "Parabéns *{$user->name}!*,\n\nVocê acabou de vencer o sorteio *{$contest->title}.*\n\nPara solictar o seu prêmio, responda essa mensagem para conversar com a nossa equipe.";

    return $this->sendMessage($user, $message);
  }

  /**
   * Send recovery password message.
   * 
   * @param User $user
   * @param sting $code
   * 
   * @return boolean
   */
  protected function sendRecoveryMessage(User $user, string $code)
  {
    $message = "Olá *{$user->name}*, tudo bem?\n\nUtilize o código abaixo para recuperar sua senha no Presente Sincero.\n\n*{$code}*";

    return $this->sendMessage($user, $message);
  }

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
}
