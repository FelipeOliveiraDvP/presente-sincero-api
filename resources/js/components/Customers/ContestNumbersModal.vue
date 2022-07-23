<template>
  <a-modal
    v-model:visible="visible"
    title="Meus números"
    ok-text="Ver meus números"
    cancel-text="Fechar"
    @ok="handleSubmit"
    @cancel="handleCancel"
  >
    <a-form
      ref="formRef"
      :model="formState"
      layout="vertical"
      name="get_user_numbers"
    >
      <a-form-item
        name="whatsapp"
        label="Informe seu WhatsApp"
        :rules="[{ required: true, message: 'Informe seu WhatsApp' }]"
      >
        <a-input v-model:value="formState.whatsapp" />
      </a-form-item>
    </a-form>
  </a-modal>
  <!-- <b-modal id="customer-numbers" title="Meus números" ok-title="Ver meus números" :ok-disabled="loading" @ok="handleOk"
    @hidden="handleClose">
    <p>Informe seu WhatsApp abaixo para conferir seus números nesse sorteio</p>
    <b-form ref="form" class="mb-3" @submit.stop.prevent="handleSubmit">
      <b-form-group>
        <b-form-input id="whatsapp" v-model="$v.whatsapp.$model" :state="validateState()"
          placeholder="Informe seu WhatsApp" />

        <b-form-invalid-feedback v-if="!$v.whatsapp.required">Campo obrigatório</b-form-invalid-feedback>
        <b-form-invalid-feedback v-if="!$v.whatsapp.minLength">WhatsApp inválido</b-form-invalid-feedback>
        <b-form-invalid-feedback v-if="!$v.whatsapp.numeric">Informe apenas números</b-form-invalid-feedback>
      </b-form-group>
    </b-form>

    <my-loader v-if="loading" />
    <div v-else>
      <b-alert :show="success === false" variant="warning">
        O número informado não é válido ou ainda não está cadastrado.
      </b-alert>

      <b-alert :show="success === true && numbers.length === 0" variant="warning">
        Você ainda não comprou nenhum número nesse sorteio.
      </b-alert>

      <b-alert :show="success === true && numbers.length > 0" variant="light">
        <h3 class="mb-3">Seus números para esse sorteio</h3>
        <p>{{ numbers.join(", ") }}</p>
      </b-alert>
    </div>

    <p class="text-muted">
      <strong>IMPORTANTE:</strong> Somente vão aparecer os números dos pedidos
      que tiveram o pagamento <strong>confirmado.</strong>
    </p>
  </b-modal> -->
</template>

<script>
import { getCustomerNumbers } from "@/services/numbers";
import { defineComponent, reactive, ref } from "@vue/runtime-core";

export default defineComponent({
  name: "ContestNumbersModal",
  setup({ contestId, visible }) {
    const loading = ref(false);
    const success = ref(false);
    const numbers = ref([]);
    const formRef = ref();
    const formState = reactive({
      whatsapp: "",
    });

    async function handleSubmit() {
      try {
        loading.value = true;

        const values = await formRef.value.validateFields();
        const result = await getCustomerNumbers(contestId, values);
        const contestNumbers = result.map((r) => JSON.parse(r.numbers));

        numbers.value = [].concat.apply([], contestNumbers);
        success.value = true;
        loading.value = false;

        formRef.value.reset();
      } catch (error) {
        success.value = false;
      } finally {
        loading.value = false;
      }
    }

    function handleClose() {
      formRef.value.reset();
    }

    return {
      visible,
      loading,
      success,
      numbers,
      formRef,
      formState,
      handleSubmit,
      handleClose,
    };
  },
});
// export default {

//   data() {
//     return {
//       loading: false,
//       success: null,
//       whatsapp: "",
//       numbers: [],
//     };
//   },
//   methods: {
//     async handleSubmit() {
//       try {
//         this.loading = true;

//         const result = await getCustomerNumbers(this.contestId, {
//           whatsapp: this.whatsapp,
//         });

//         const contestNumbers = result.map((r) => JSON.parse(r.numbers));

//         this.numbers = [].concat.apply([], contestNumbers);

//         this.success = true;
//       } catch (error) {
//         this.success = false;
//       } finally {
//         this.loading = false;
//       }
//     },
//     handleOk(e) {
//       e.preventDefault();

//       this.handleSubmit();
//     },
//     handleClose() {
//       this.whatsapp = "";
//       this.success = null;
//       this.numbers = [];
//     },
//   },
// };
</script>

<style>
</style>