export interface UploadResponse {
  message: string;
  image: {
    id: number;
    title: string;
    path: string;
  };
}
