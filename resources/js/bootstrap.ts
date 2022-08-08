window.Pusher = require("pusher-js");

import Echo from "laravel-echo";

window.Echo = new Echo({
  broadcaster: "pusher",
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
  authEndpoint: "/broadcasting/auth",
});
