<template>
  <div>
    <h3>Imagens</h3>

    <div class="my-2">
      <font-awesome-icon icon="fa-solid fa-upload" /> Carregar imagem
      <b-form-file
        v-model="file"
        class="mt-3"
        plain
        @change="upload"
        ref="file"
      ></b-form-file>
    </div>

    <b-row>
      <b-col
        v-for="(image, index) in contest.gallery"
        :key="`image-${index}`"
        md="6"
        lg="4"
        xl="3"
      >
        <img
          :src="image.image_path"
          class="img-fluid"
          :class="{ thumbnail: image.thumbnail }"
        />
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { uploadImage } from "../../../services/upload";

export default {
  props: {
    contest: Object,
    addImage: Function,
  },
  name: "GalleryForm",
  data() {
    return {
      loading: true,
      file: null,
    };
  },
  methods: {
    async upload(e) {
      try {
        this.loading = true;

        const formData = new FormData();
        const file = e.target.files[0];

        formData.append("image", file);

        const response = await uploadImage(formData);
        const { path } = response.image;

        this.addImage({
          image_path: path,
        });

        this.$toasted.show(response.message, {
          type: "success",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      } catch (error) {
        this.$toasted.show(error.message, {
          type: "error",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style>
</style>