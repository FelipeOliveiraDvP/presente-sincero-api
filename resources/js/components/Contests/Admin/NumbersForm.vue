<template>
  <div>
    <h3>Números</h3>

    <b-row>
      <b-col md="6">
        <b-form-group
          id="number_price"
          label="Valor do número R$"
          label-for="number_price"
        >
          <b-form-input
            id="number_price"
            v-model="v.contest.number_price.$model"
            type="text"
            :state="validateState('number_price')"
          ></b-form-input>
          <b-form-invalid-feedback v-if="!v.contest.number_price.required"
            >Campo obrigatório</b-form-invalid-feedback
          >
        </b-form-group>
      </b-col>
      <b-col md="6">
        <b-form-group
          id="number_start"
          label="Número inicial do sorteio"
          label-for="number_start"
        >
          <b-form-input
            id="number_start"
            v-model="v.contest.number_start.$model"
            type="text"
            :state="validateState('number_start')"
          ></b-form-input>
          <b-form-invalid-feedback v-if="!v.contest.number_start.required"
            >Campo obrigatório</b-form-invalid-feedback
          >
        </b-form-group>
      </b-col>
      <b-col md="6">
        <b-form-group
          id="number_quantity"
          label="Quantidade de números disponíveis"
          label-for="number_quantity"
        >
          <b-form-input
            id="number_quantity"
            v-model="v.contest.number_quantity.$model"
            type="text"
            :state="validateState('number_quantity')"
          ></b-form-input>
          <b-form-invalid-feedback v-if="!v.contest.number_quantity.required"
            >Campo obrigatório</b-form-invalid-feedback
          >
        </b-form-group>
      </b-col>
      <b-col md="6">
        <b-form-group
          id="number_per_customer"
          label="Quantidade de números por cliente"
          label-for="number_per_customer"
          description="O cliente será notificado do limite durante a escolha dos números"
        >
          <b-form-spinbutton
            id="number_per_customer"
            v-model="contest.number_per_customer"
            locale="pt-BR"
            min="1"
            step="1"
          ></b-form-spinbutton>
        </b-form-group>
      </b-col>
    </b-row>
  </div>
</template>

<script>
export default {
  props: {
    v: Object,
    contest: Object,
    validateState: Function,
  },
  name: "NumbersForm",
};
</script>

<style>
</style>