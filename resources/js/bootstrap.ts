import Echo from "laravel-echo";

const meta = document.querySelector("meta[name=csrf-token]");
const token = meta.getAttribute("content");

window.Pusher = require("pusher-js");

window.Echo = new Echo({
  broadcaster: "pusher",
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
  authEndpoint: "/broadcasting/auth",
  auth: {
    headers: {
      "X-CSRF-Token": token,
    },
  },
});
