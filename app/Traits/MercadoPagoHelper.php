<?php

namespace App\Traits;

use App\Models\Contest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use MercadoPago\SDK;
use MercadoPago\Payment;

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

    $notification = env('APP_ENV') == 'local' ? env('WEBHOOK_MERCADO_PAGO') : env('APP_URL') . "/api/numbers/{$contest->id}/paid";
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
      'qrcode_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
      'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url,
      'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
    ];
  }

  /**
   * Proccess Mercado Pago Callback from WebHook.
   * 
   * @param Request $request
   * 
   * @return array|boolean
   */
  protected function callback(Request $request)
  {
    if ($request->type == 'payment' && $request->status == 'approved') {
      $contest_id = explode('_', $request->external_reference)[1];

      return $contest_id;
    }

    return false;
  }
}
