<template>
  <div>
    <h3>Detalhes do sorteio</h3>

    <b-form-group label="Título" label-for="title">
      <b-form-input
        id="title"
        name="title"
        v-model="v.contest.title.$model"
        :state="validateState('title')"
      ></b-form-input>

      <b-form-invalid-feedback v-if="!v.contest.title.required"
        >Campo obrigatório</b-form-invalid-feedback
      >
    </b-form-group>

    <b-form-group
      id="short_description"
      label="Descrição curta"
      label-for="short_description"
    >
      <b-form-input
        id="short_description"
        v-model="v.contest.short_description.$model"
        type="text"
        placeholder="Descreva o sorteio em poucas palavras"
        :state="validateState('short_description')"
      ></b-form-input>

      <b-form-invalid-feedback v-if="!v.contest.short_description.required"
        >Campo obrigatório</b-form-invalid-feedback
      >
    </b-form-group>

    <b-form-group
      id="full_description"
      label="Descrição completa"
      label-for="full_description"
    >
      <b-form-textarea
        id="full_description"
        v-model="v.contest.full_description.$model"
        placeholder="Descreva em detalhes as premiações e como funciona o sorteio"
        rows="3"
        max-rows="6"
        :state="validateState('full_description')"
      ></b-form-textarea>

      <b-form-invalid-feedback v-if="!v.contest.full_description.required"
        >Campo obrigatório</b-form-invalid-feedback
      >
    </b-form-group>

    <b-row>
      <b-col md="4">
        <b-form-group
          id="start_date"
          label="Data de início"
          label-for="start_date"
        >
          <b-form-datepicker
            id="start_date"
            v-model="v.contest.start_date.$model"
            class="mb-2"
            required
            placeholder="Escolha uma data"
            :state="validateState('start_date')"
          ></b-form-datepicker>
          <b-form-invalid-feedback v-if="!v.contest.start_date.required"
            >Campo obrigatório</b-form-invalid-feedback
          >
        </b-form-group>
      </b-col>
      <b-col md="4">
        <b-form-group
          id="days_to_end"
          label="Dias até o final do sorteio"
          label-for="days_to_end"
        >
          <b-form-spinbutton
            id="days_to_end"
            v-model="contest.days_to_end"
            locale="pt-BR"
            min="1"
            max="90"
            step="1"
          ></b-form-spinbutton>
        </b-form-group>
      </b-col>
      <b-col md="4">
        <b-form-group
          id="contest_date"
          label="Data do sorteio"
          label-for="contest_date"
        >
          <b-form-datepicker
            id="contest_date"
            v-model="v.contest.contest_date.$model"
            class="mb-2"
            required
            placeholder="Escolha uma data"
            :state="validateState('contest_date')"
          ></b-form-datepicker>
          <b-form-invalid-feedback v-if="!v.contest.contest_date.required"
            >Campo obrigatório</b-form-invalid-feedback
          >
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
  name: "DetailForm",
};
</script>

<style>
</style>