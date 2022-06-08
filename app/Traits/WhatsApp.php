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
    $message = "Parabéns *{$user->name}!*,\n\n";
    $message .= "Você acabou de vencer o sorteio *{$contest->title}.*\n\n";
    $message .= "Para solictar o seu prêmio, responda essa mensagem para conversar com a nossa equipe.";

    return $this->sendMessage($user, $message);
  }

  /**
   * Send numbers reservation message.
   * 
   * @param User $user
   * @param Contest $contest
   * @param array $payment_info
   * 
   * @return boolean
   */
  protected function sendReservationMessage(User $user, Contest $contest, array $payment_info)
  {
    $message = "Olá *{$user->name}*,\n\n";
    $message .= "Seus números no sorteio *{$contest->title}.* foram reservados e estamos aguardando a confirmação do pagamento.\n\n";
    $message .= "Por favor, realize o pagamento no link abaixo para concorrer ao sorteio.\n\n";
    $message .= $payment_info['ticket_url'] . "\n\n";
    $message .= "*Equipe Presente Sincero*";

    return $this->sendMessage($user, $message);
  }

  /**
   * Send confirmation payment message.
   * 
   * @param User $user
   * @param Contest $contest
   * @param array $payment_info
   * 
   * @return boolean
   */
  protected function sendConfirmationMessage(User $user, Contest $contest, array $payment_info)
  {
    $message = "Olá *{$user->name}*,\n\n";
    $message .= "Recebemos o seu pagamento do sorteio *{$contest->title}.*\n\n";
    $message .= "Agora é só acompanhar o resultado no grupo do sorteio no WhatsApp e torcer 🤞";

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
    $message = "Olá *{$user->name}*, tudo bem?\n\n";
    $message .= "Utilize o código abaixo para recuperar sua senha no Presente Sincero.\n\n";
    $message .= "*{$code}*";

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
