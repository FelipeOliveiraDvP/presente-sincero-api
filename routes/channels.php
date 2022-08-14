<?php

use App\Broadcasting\OrderChannel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('payment.confirmed', function () {
    return true;
});

Broadcast::channel('payment.manual', function () {
    return true;
});

Broadcast::channel('payment.information', function () {
    return true;
});

Broadcast::channel('test.connection', OrderChannel::class);
