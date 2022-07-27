declare global {
  namespace NodeJS {
    interface Process {
      MIX_ADMIN_ROLE: string;
      MIX_SELLER_ROLE: string;
      MIX_CUSTOMER_ROLE: string;
    }
  }
}

export {};
