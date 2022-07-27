export {};

declare global {
  interface Window {
    Pusher: any;
    Echo: any;
  }

  namespace NodeJS {
    interface Process {
      MIX_ADMIN_ROLE: string;
      MIX_SELLER_ROLE: string;
      MIX_CUSTOMER_ROLE: string;
    }
  }
}
