<template>
  <a-form-item>
    <div class="clearfix">
      <a-upload
        v-model:file-list="fileList"
        list-type="picture-card"
        :multiple="true"
        :customRequest="handleUpload"
        @preview="handlePreview"
        @remove="handleRemove"
      >
        <div v-if="fileList.length < 8">
          <upload-outlined />
          <div style="margin-top: 8px">Upload</div>
        </div>
      </a-upload>
      <a-modal
        :visible="previewVisible"
        :title="previewTitle"
        :footer="null"
        @cancel="handleCancel"
      >
        <img alt="example" style="width: 100%" :src="previewImage" />
      </a-modal>
    </div>
  </a-form-item>
</template>

<script lang="ts">
import { defineComponent, ref } from "@vue/runtime-core";
import { UploadOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import type { UploadProps } from "ant-design-vue";

import { uploadImage } from "@/services/upload";

function getBase64(file: File) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = (error) => reject(error);
  });
}

export default defineComponent({
  name: "ContestGalleryForm",
  components: {
    UploadOutlined,
  },
  emits: ["change"],
  setup(_, { emit }) {
    const previewVisible = ref(false);
    const previewImage = ref("");
    const previewTitle = ref("");
    const fileList = ref<UploadProps["fileList"]>([]);
    const imageList = ref<{ uid: string; path: string }[]>([]);

    async function handleUpload(e: any) {
      const { file, onSuccess, onError } = e;
      try {
        const formData = new FormData();

        formData.append("image[0]", file as File);

        const result = await uploadImage(formData);

        message.success(result.message);
        onSuccess(null);

        imageList.value.push({
          uid: file.uid,
          path: result.images[0].path,
        });

        emit(
          "change",
          imageList.value.map((img) => img.path)
        );
      } catch (error) {
        onError(null);
      }
    }

    const handleCancel = () => {
      previewVisible.value = false;
      previewTitle.value = "";
    };

    const handlePreview = async (file: UploadProps["fileList"][number]) => {
      if (!file.url && !file.preview) {
        file.preview = (await getBase64(file.originFileObj)) as string;
      }
      previewImage.value = file.url || file.preview;
      previewVisible.value = true;
      previewTitle.value =
        file.name || file.url.substring(file.url.lastIndexOf("/") + 1);
    };

    const handleRemove = (file: any) => {
      const removed = imageList.value.find((img) => img.uid === file.uid);
      const index = imageList.value.indexOf(removed);

      imageList.value.splice(index, 1);

      emit(
        "change",
        imageList.value.map((img) => img.path)
      );
    };

    return {
      previewTitle,
      previewVisible,
      previewImage,
      fileList,
      handleCancel,
      handlePreview,
      handleUpload,
      handleRemove,
    };
  },
});
</script>

<style>
</style>