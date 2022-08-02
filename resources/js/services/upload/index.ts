import api from "@/api";
import { UploadResponse } from "../../types/Upload.types";

export async function uploadImage(formData: FormData) {
  const result = (await api.upload("upload/image", formData)) as unknown;

  return result as UploadResponse;
}
