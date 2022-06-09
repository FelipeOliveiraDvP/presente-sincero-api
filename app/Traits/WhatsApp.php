<?php

namespace App\Traits;

use App\Models\Contest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;

// TODO: Estudar possibilidade de trocar para o Twilio
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
    $message .= "Seus números no sorteio *{$contest->title}* foram reservados com sucesso!\n";
    $message .= "Caso ainda não tenha realizado o pagamento, é só clicar no link abaixo para abrir o QR Code.\n\n";
    $message .= $payment_info['ticket_url'];
    $message .= "\n\nCaso já tenha realizado o pagamento, é só aguardar a confirmação.";
    $message .= "\n\n*Equipe Presente Sincero*";


    return $this->sendMessage($user, $message);
  }

  /**
   * Send confirmation payment message.
   * 
   * @param User $user
   * @param Contest $contest
   * @param Order $order   
   * 
   * @return boolean
   */
  protected function sendConfirmationMessage(User $user, Contest $contest, Order $order)
  {
    $customer_numbers = json_decode($order->numbers);

    $message = "Olá *{$user->name}*,\n\n";
    $message .= "Recebemos o seu pagamento do sorteio *{$contest->title}.*\n\n";
    $message .= "Agora é só acompanhar o resultado no grupo do sorteio no WhatsApp e torcer 🤞";

    $mensagem = "Olá, {$user->name}\n";
    $mensagem .= "*PARABÉNS* 🎉🍾, seu pagamento foi confirmado com sucesso.!\n";
    $mensagem .= "*{$contest->title}.*\n";
    $mensagem .= "Entre no grupo dos participantes do sorteio através do link abaixo:\n\n";
    $mensagem .= $contest->whatsapp_group;
    $mensagem .= "\n*IMPORTANTE:* É obrigatório permanecer no grupo até o término do sorteio\n\n";

    $mensagem .= "Seu(s) número(s): " . join(',', $customer_numbers) . "\n";
    $mensagem .= "Valor Total: R$ " . number_format($order->total, 2, ',', '.') . "\n";
    $mensagem .= "Data do Pagamento: " . date('d/m/Y H:i', strtotime($contest->updated_at)) . "\n\n";

    $mensagem .= "*Atenciosamente, equipe Presente Sincero*";

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
