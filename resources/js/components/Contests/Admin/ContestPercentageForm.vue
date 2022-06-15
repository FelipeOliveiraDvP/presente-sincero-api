<template>
  <div>
    <h3>Andamento do concurso</h3>
    <p class="text-muted">
      Aqui você pode editar a porcentagem do concurso, e escolher se vai exibir
      a porcentagem real ou um valor customizado.
    </p>
    <b-form-group label="Selecione uma opção">
      <b-form-radio
        v-model="selected"
        name="percentage"
        value="hidden"
        @change="handleChangePercentage"
        >Esconder a barra de porcentagem</b-form-radio
      >
      <b-form-radio
        v-model="selected"
        name="percentage"
        value="use_true"
        @change="handleChangePercentage"
        >Mostar a porcentagem real</b-form-radio
      >
      <b-form-radio
        v-model="selected"
        name="percentage"
        value="use_custom"
        @change="handleChangePercentage"
        >Definir a porcentagem customizada</b-form-radio
      >
    </b-form-group>

    <div v-if="selected === 'use_custom'">
      <label for="contest-percentage">{{ percentage * 100 }}%</label>
      <b-form-input
        id="contest-percentage"
        class="d-block w-100 mb-4"
        v-model="percentage"
        type="range"
        min="0"
        max="1"
        step="0.01"
        @change="handleChangePercentage"
      ></b-form-input>
    </div>
  </div>
</template>

<script>
export default {
  name: "ContestPercentageForm",
  props: {
    percentageInfo: Object,
  },
  data() {
    return {
      selected: "hidden",
      percentage: 0,
    };
  },
  mounted() {
    if (this.percentageInfo.show_percentage) {
      this.selected = "use_true";
      this.percentage = this.percentageInfo.paid_percentage;
    } else if (this.percentageInfo.use_custom_percentage) {
      this.selected = "use_custom";
      this.percentage = this.percentageInfo.custom_percentage;
    }
  },
  methods: {
    handleChangePercentage() {
      this.$emit("changePercentage", {
        selected: this.selected,
        percentage: this.percentage,
      });
    },
  },
};
</script>

<style>
</style>