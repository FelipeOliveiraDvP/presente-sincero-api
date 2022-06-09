import api from "@/api";

export async function uploadImage(formData) {
  return api.upload("upload/image", formData);
}
