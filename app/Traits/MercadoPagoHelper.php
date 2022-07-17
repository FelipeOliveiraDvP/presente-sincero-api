<?php

namespace App\Traits;

use App\Models\Contest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Config;

use MercadoPago;

// TODO: Gerenciar comissões para aplicação no Mercado Pago e contas dos vendedores.
trait MercadoPagoHelper
{
  /**
   * Create a Mercado Pago PIX payment.
   * 
   * @param Order $order, 
   * @param User $user, 
   * @param Contest $contest
   * 
   * @return array
   */
  protected function createPayment(Order $order, User $user, Contest $contest)
  {
    $user = User::find($contest->user_id);

    MercadoPago\SDK::setAccessToken($user->mp_access_token);

    $payment = new MercadoPago\Payment();

    $expiration = date('Y-m-d\TH:i:s.vP', strtotime("+{$contest->max_reserve_days} days"));

    $payment->transaction_amount = $order->total;
    $payment->description = $contest->title;
    $payment->external_reference = $order->id;
    $payment->payment_method_id = "pix";
    $payment->date_of_expiration = $expiration;
    $payment->notification_url = Config::get('ps.MERCADO_PAGO_WEBHOOK');
    $payment->payer = [
      'first_name' => $user->name,
      'last_name' => "cliente-{$user->id}",
      'email' => $user->email ?? 'test@email.com',
      'identification' => [
        'type' => 'customer',
        'number' => $user->whatsapp,
      ]
    ];

    $payment->save();

    return [
      'payment_id' => $payment->id,
      'order_id' => $order->id,
      'qrcode_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
      'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url,
      'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
    ];
  }

  /**
   * Proccess Mercado Pago Callback from WebHook and return false if not approved or order id is paid.
   * 
   * @param $data
   * 
   * @return string|boolean
   */
  protected function callback($data)
  {
    if (empty($data) || is_null($data)) return false;

    $order = Order::where('transaction_code', '=', $data['data']['id'])->first();
    $contest = Contest::find($order->contest_id);
    $user = User::find($contest->user_id);

    MercadoPago\SDK::setAccessToken($user->mp_access_token);

    $payment = MercadoPago\Payment::find_by_id($order->transaction_code);

    if ($data['type'] == 'payment' && $payment->status == 'approved') {
      return $payment->external_reference;
    }

    return false;
  }
}
