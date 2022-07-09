<?php

/*
|--------------------------------------------------------------------------
| Presente sincero
|--------------------------------------------------------------------------
| 
| Este arquivo realiza o mapeamento para as variáveis de ambiente 
| específicas para o projeto
| 
|
*/
return [
  /**
   * Mercado Pago Config
   */
  'MERCADO_PAGO_WEBHOOK' => env('MERCADO_PAGO_WEBHOOK'),

  /**
   * API ChatPRO Config
   */
  'API_CHATPRO_URL' => env('API_CHATPRO_URL'),
  'API_CHATPRO_TOKEN' => env('API_CHATPRO_TOKEN'),
];
