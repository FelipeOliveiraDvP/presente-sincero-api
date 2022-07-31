<template>
  <a-page-header title="Novo sorteio" />

  <a-form
    ref="formRef"
    layout="vertical"
    :model="formState"
    @finish="handleSubmit"
  >
    <a-row :gutter="[16, 16]">
      <a-col :span="24">
        <a-form-item
          label="Título"
          name="title"
          :rules="[{ required: true, message: 'Campo obrigatório!' }]"
        >
          <a-input v-model:value="formState.title" />
        </a-form-item>
      </a-col>

      <a-col :span="24">
        <a-form-item
          label="Descrição"
          name="short_description"
          :rules="[{ required: true, message: 'Campo obrigatório!' }]"
        >
          <a-textarea
            v-model:value="formState.short_description"
            placeholder="Descreva quais são as regras do sorteio e outras informações"
            :auto-size="{ minRows: 4, maxRows: 5 }"
          />
        </a-form-item>
      </a-col>

      <a-col :xs="24" :md="8">
        <a-form-item
          label="Data de início"
          name="start_date"
          :rules="[{ required: true, message: 'Campo obrigatório!' }]"
        >
          <a-date-picker
            v-model:value="formState.start_date"
            style="width: 100%"
          />
        </a-form-item>
      </a-col>

      <a-col :xs="24" :md="8">
        <a-form-item
          label="R$ Valor"
          name="price"
          :validateFirst="true"
          :rules="[{ validator: moneyValidator, trigger: ['change', 'blur'] }]"
        >
          <input-money v-model="formState.price" />
        </a-form-item>
      </a-col>

      <a-col :xs="24" :md="8">
        <a-form-item label="Quantidade" name="quantity">
          <a-input
            v-model:value="formState.quantity"
            type="number"
            :min="100"
          />
        </a-form-item>
      </a-col>

      <a-col :xs="24" :md="8">
        <a-form-item
          label="Dias para a reserva"
          name="max_reserve_days"
          help="Quantidade de dias que o pagamento gerado vai ficar disponível. (Somente para o Mercado Pago)"
        >
          <a-input
            v-model:value="formState.max_reserve_days"
            type="number"
            :min="1"
          />
        </a-form-item>
      </a-col>

      <a-col :xs="24" :md="8">
        <a-form-item
          label="WhatsApp para contato"
          name="whatsapp_number"
          help="Seu clientes vão entrar em contato com esse número para enviar comprovantes ou em caso de dúvida"
          :rules="[
            { required: true, message: 'Inform um WhatsApp para contato' },
          ]"
        >
          <a-input v-model:value="formState.whatsapp_number" />
        </a-form-item>
      </a-col>

      <a-col :xs="24" :md="8">
        <a-form-item
          label="Grupo do sorteio"
          name="whatsapp_group"
          help="Após a confirmação do pagamento, seus clientes devem participar desse grupo para acompanhar o sorteio"
          :rules="[
            { required: true, message: 'Informe o grupo para o sorteio' },
          ]"
        >
          <a-input v-model:value="formState.whatsapp_group" />
        </a-form-item>
      </a-col>
    </a-row>

    <a-divider />
    <!-- Promoções -->
    <a-typography-title :level="3">Promoções</a-typography-title>

    <a-typography-text type="secondary"
      >Adicione promoções para impulsionar as vendas.</a-typography-text
    ><br />

    <a-typography
      ><strong>Como funciona?: </strong> Vamos supor que você tenha um sorteio
      de 3000 números à R$ 5,00. É possível criar uma promoção onde a partir de
      100 números, cada um vai sair por R$ 2,50. </a-typography
    ><br />

    <a-space
      v-for="(sale, index) in formState.sales"
      :key="index"
      style="display: flex; margin-bottom: 8px"
      align="baseline"
    >
      <a-form-item :name="['sales', index, 'quantity']">
        <a-input v-model:value="sale.quantity" type="number" :min="1" />
      </a-form-item>
      <a-form-item
        :name="['sales', index, 'price']"
        :validateFirst="true"
        :rules="[{ validator: moneyValidator, trigger: ['change', 'blur'] }]"
      >
        <input-money v-model="sale.price" />
      </a-form-item>
      <a-button size="small" type="primary" danger @click="removeSale(sale)">
        <delete-outlined />
      </a-button>
    </a-space>
    <a-form-item>
      <a-button type="primary" @click="addSale" size="large">
        <plus-outlined />
        Adicionar promoção
      </a-button>
    </a-form-item>
    <!-- Contas bancárias -->

    <!-- Galeria -->

    <a-form-item>
      <a-button type="primary" html-type="submit">Cadastrar sorteio</a-button>
    </a-form-item>
  </a-form>
</template>

<script lang="ts">
import { defineComponent, reactive, ref, watch } from "@vue/runtime-core";
import { FormInstance } from "ant-design-vue";
import type { Rule } from "ant-design-vue/lib/form";
import { DeleteOutlined, PlusOutlined } from "@ant-design/icons-vue";

import InputMoney from "@/components/_commons/InputMoney.vue";
import { ContestRequest, ContestSale } from "@/types/Contest.types";

export default defineComponent({
  components: { InputMoney, DeleteOutlined, PlusOutlined },
  name: "AdminContestCreate",
  setup() {
    const formRef = ref<FormInstance>();
    const formState = reactive<ContestRequest>({
      title: "",
      short_description: "",
      start_date: "",
      price: 0.1,
      quantity: 100,
      max_reserve_days: 1,
      whatsapp_number: "",
      whatsapp_group: "",
      sales: [] as ContestSale[],
      bank_accounts: [],
      gallery: [],
    });

    async function handleSubmit(values: ContestRequest) {
      console.log(values);
    }

    function addSale() {
      formState.sales.push({
        quantity: 1,
        price: 0.1,
      } as ContestSale);
      // const sales: ContestSale[] = [
      //   {
      //     quantity: 1,
      //     price: 0.1,
      //   },
      // ];
      // formState.sales = sales;
    }

    function removeSale(sale: any) {
      let index = formState.sales.indexOf(sale as ContestSale);

      if (index !== -1) {
        formState.sales.splice(index, 1);
      }
    }

    const moneyValidator = async (_rule: Rule, value: string) => {
      if (value === "") {
        return Promise.reject("Campo obrigatório!");
      } else {
        if (formState.price < 0.1 || formState.price === null) {
          return Promise.reject("O preço deve ser maior que R$ 0,10");
        }
        return Promise.resolve();
      }
    };

    watch(formState, (newVal) => {
      formState.quantity = parseInt(String(newVal.quantity), 10);
    });

    watch(formState, (newVal) => {
      formState.max_reserve_days = parseInt(
        String(newVal.max_reserve_days),
        10
      );
    });

    return {
      formRef,
      formState,
      moneyValidator,
      handleSubmit,
      addSale,
      removeSale,
    };
  },
});
</script>

<style lang="scss">
</style>s