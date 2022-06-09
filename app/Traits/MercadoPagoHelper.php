<?php

namespace App\Traits;

use App\Models\Contest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use MercadoPago\SDK;
use MercadoPago\Payment;

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
    SDK::setAccessToken("APP_USR-2306247977509923-042715-d807d8e7e04369b64d2258788140bfc5-1111566559");

    $payment = new Payment();

    $notification = getenv('APP_ENV') == 'local' ? getenv('WEBHOOK_MERCADO_PAGO') : getenv('APP_URL') . "/api/webhook";
    $expiration = date('Y-m-d\TH:i:s.vP', strtotime("+{$contest->max_reserve_days} days"));

    $payment->transaction_amount = $order->total;
    $payment->description = $contest->title;
    $payment->external_reference = $order->id;
    $payment->payment_method_id = "pix";
    $payment->date_of_expiration = $expiration;
    $payment->notification_url = $notification;
    $payment->payer = [
      'email' => $user->email,
      'first_name' => $user->name,
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

    SDK::setAccessToken("APP_USR-2306247977509923-042715-d807d8e7e04369b64d2258788140bfc5-1111566559");

    $payment = Payment::find_by_id($request->data->id);

    if ($request->type == 'payment' && $payment->status == 'approved') {
      return $payment->external_reference;
    }

    return false;
  }
}
