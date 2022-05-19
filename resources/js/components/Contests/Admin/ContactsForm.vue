<template>
  <div>
    <h3>Contatos</h3>

    <b-row
      v-for="(contact, index) in contest.contacts"
      :key="`contact-${index}`"
    >
      <b-col md="2">
        <b-form-group id="contact_name" label="Nome" label-for="contact_name">
          <b-form-input
            id="contact_name"
            v-model="contact.name"
            type="text"
            :state="null"
          ></b-form-input>
          <!-- <b-form-invalid-feedback v-if="!v.contest.contact_name.required"
            >Campo obrigatório</b-form-invalid-feedback
          > -->
        </b-form-group>
      </b-col>
      <b-col md="8">
        <b-form-group
          id="contact_value"
          label="Descrição"
          label-for="contact_value"
        >
          <b-form-input
            id="contact_value"
            v-model="contact.value"
            type="text"
            :state="null"
          ></b-form-input>
        </b-form-group>
      </b-col>
      <b-col v-if="index > 0" md="2" class="d-flex align-items-end">
        <b-button variant="danger" @click="removeContact(index)">
          <font-awesome-icon icon="fa-solid fa-trash" />
        </b-button>
      </b-col>
    </b-row>

    <b-button variant="primary" class="my-4" @click="addContact"
      >Novo contato</b-button
    >
  </div>
</template>

<script>
export default {
  props: {
    v: Object,
    contest: Object,
    validateState: Function,
    addContact: Function,
    removeContact: Function,
  },
  name: "ContactsForm",
};
</script>

<style>
</style>