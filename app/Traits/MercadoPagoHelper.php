<?php

namespace App\Traits;

use App\Models\Contest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
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
    MercadoPago\SDK::setAccessToken("APP_USR-2306247977509923-042715-d807d8e7e04369b64d2258788140bfc5-1111566559");

    $payment = new MercadoPago\Payment();

    $notification = getenv('APP_ENV') == 'local' ? getenv('MERCADO_PAGO_WEBHOOK') : getenv('APP_URL') . "/api/webhook";
    $expiration = date('Y-m-d\TH:i:s.vP', strtotime("+{$contest->max_reserve_days} days"));

    $payment->transaction_amount = $order->total;
    $payment->description = $contest->title;
    $payment->external_reference = $order->id;
    $payment->payment_method_id = "pix";
    $payment->date_of_expiration = $expiration;
    $payment->notification_url = $notification;
    $payment->payer = [
      'first_name' => $user->name,
      'last_name' => 'ps',
      'email' => $user->email ?? 'test@email.com',
      'identification' => [
        'type' => 'customer',
        'number' => $user->whatsapp,
      ]
    ];

    $payment->save();

    return [
      'payment_id' => $payment->id,
      'qrcode_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
      'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url,
      'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
    ];
  }

  /**
   * Proccess Mercado Pago Callback from WebHook and return false if not approved or order id is paid.
   * 
   * @param Request $request
   * 
   * @return string|boolean
   */
  protected function callback(Request $request)
  {
    if (empty($request->data)) return false;

    MercadoPago\SDK::setAccessToken("APP_USR-2306247977509923-042715-d807d8e7e04369b64d2258788140bfc5-1111566559");

    $payment = MercadoPago\Payment::find_by_id($request->data->id);

    if ($request->type == 'payment' && $payment->status == 'approved') {
      return $payment->external_reference;
    }

    return false;
  }
}
