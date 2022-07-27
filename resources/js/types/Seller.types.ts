export interface SellerItem {
  id: number;
  name: string;
  whatsapp: string;
  email: string;
  avatar: string | null;
  seller_approved: boolean;
  blocked: boolean;
  created_at: string | null;
  updated_at: string | null;
}
