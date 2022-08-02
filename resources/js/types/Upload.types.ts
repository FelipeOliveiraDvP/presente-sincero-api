export interface UploadResponse {
  message: string;
  images: Array<{
    id: number;
    title: string;
    path: string;
  }>;
}
