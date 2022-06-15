<template>
  <div class="contest-gallery">
    <h3 class="mt-2">Imagens</h3>

    <b-row>
      <b-col md="6" lg="4" xl="3">
        <div class="image-upload">
          <label for="upload">
            <font-awesome-icon icon="fa-solid fa-upload" />
            <p>Carregar imagem</p>
          </label>
          <b-form-file
            id="upload"
            v-model="file"
            class="mt-3"
            plain
            @change="upload"
            ref="file"
          ></b-form-file>
        </div>
      </b-col>
      <b-col
        v-for="(image, index) in contest.gallery"
        class="mb-3"
        :key="`image-${index}`"
        md="6"
        lg="4"
        xl="3"
      >
        <div class="img-wrapper">
          <img :src="image.image_path || image.path" class="img-fluid" />
          <b-button
            class="remove-btn"
            variant="danger"
            @click="removeImage(index)"
          >
            <font-awesome-icon icon="fa-solid fa-trash" />
          </b-button>
        </div>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { uploadImage } from "@/services/upload";

export default {
  props: {
    contest: Object,
    addImage: Function,
    removeImage: Function,
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