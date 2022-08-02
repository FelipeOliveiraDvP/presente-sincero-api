export interface CustomerItem {
  id: number;
  name: string;
  whatsapp: string;
  email: string | null;
  avatar: string | null;
  blocked: boolean;
  created_at: string | null;
  updated_at: string | null;
}
