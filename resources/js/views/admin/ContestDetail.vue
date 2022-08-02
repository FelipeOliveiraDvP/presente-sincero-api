<template>
  <a-page-header :title="pageTitle" />

  <a-form
    ref="formRef"
    layout="vertical"
    :model="formState"
    @finish="handleSubmit"
  >
    <a-row :gutter="[16, 16]">
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

      <a-col :xs="24" :md="12" :lg="6">
        <a-form-item label="Data do sorteio" name="contest_date">
          <a-date-picker
            v-model:value="formState.contest_date"
            style="width: 100%"
            format="DD/MM/YYYY"
            :disabledDate="disabledDate"
          />
        </a-form-item>
      </a-col>

      <a-col :xs="24" :md="12" :lg="6">
        <a-form-item label="Dias para a reserva" name="max_reserve_days">
          <a-input
            v-model:value="formState.max_reserve_days"
            type="number"
            :min="1"
            :max="30"
          />
          <a-popover title="Dias para a reserva">
            <template #content>
              <p style="max-width: 300px">
                Quantidade de dias que o pagamento gerado vai ficar disponível.
                (Somente para o Mercado Pago)
              </p>
            </template>
            <a-typography-text type="secondary">
              <info-circle-outlined />
              Precisa de ajuda?
            </a-typography-text>
          </a-popover>
        </a-form-item>
      </a-col>

      <a-col :xs="24" :md="12" :lg="6">
        <a-form-item
          label="WhatsApp para contato"
          name="whatsapp_number"
          :rules="[
            { required: true, message: 'Inform um WhatsApp para contato' },
            { pattern: /^[0-9]{11}$/, message: 'Informe um número válido' },
          ]"
        >
          <a-input v-model:value="formState.whatsapp_number" />
          <a-popover title="WhatsApp para contato">
            <template #content>
              <p style="max-width: 300px">
                Seu clientes vão entrar em contato com esse número para enviar
                comprovantes ou em caso de dúvida
              </p>
              <p>
                <strong>Importante:</strong> Certifique-se de informar um número
                correto
              </p>
            </template>
            <a-typography-text type="secondary">
              <info-circle-outlined />
              Precisa de ajuda?
            </a-typography-text>
          </a-popover>
        </a-form-item>
      </a-col>

      <a-col :xs="24" :md="12" :lg="6">
        <a-form-item
          label="Grupo do sorteio"
          name="whatsapp_group"
          :rules="[{ type: 'url', message: 'Informe uma URL válida' }]"
        >
          <a-input v-model:value="formState.whatsapp_group" />
          <a-popover title="Grupo do sorteio">
            <template #content>
              <p style="max-width: 300px">
                Após a confirmação do pagamento, seus clientes devem participar
                desse grupo para acompanhar o sorteio
              </p>
            </template>
            <a-typography-text type="secondary">
              <info-circle-outlined />
              Precisa de ajuda?
            </a-typography-text>
          </a-popover>
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
        :rules="[
          { validator: salePriceValidator, trigger: ['change', 'blur'] },
        ]"
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
    <a-typography-title :level="3">Contas bancárias</a-typography-title>

    <a-typography-text type="secondary"
      >Escolha as contas onde você vai receber os pagamentos dos
      pedidos.</a-typography-text
    ><br />

    <a-typography
      ><strong>Como funciona?: </strong> Após selecionar os números, o cliente é
      levado para a página do checkout. Caso você tenha configurado o pagamento
      automático, o cliente vai visualizar um QR Code para o pagamento. <br />
      Do contrário, vão ser exibidas as contas selecionadas abaixo onde o
      cliente deverá enviar o comprovante para a confirmação do pedido. </a-typography
    ><br />

    <contest-bank-account-form
      :bankAccounts="bankAccounts"
      @select="handleSelectAccount"
    />

    <!-- Galeria -->
    <a-typography-title :level="3">Galeria de imagens</a-typography-title>

    <a-typography-text type="secondary"
      >Escolha as melhores fotos que representam o seu
      sorteio</a-typography-text
    ><br />

    <a-typography
      ><strong>Como funciona?: </strong> Selecione todas as fotos que deseja
      adicionar ao sorteio. </a-typography
    ><br />

    <contest-gallery-form :images="gallery" @change="handleChangeImages" />

    <a-form-item>
      <a-button type="primary" html-type="submit" :loading="loading"
        >Salvar alterações</a-button
      >
    </a-form-item>
  </a-form>
</template>

<script lang="ts">
import { editContest, getContest } from "@/services/contests";
import {
  ContestDetail,
  ContestEditRequest,
  ContestSale,
} from "@/types/Contest.types";
import { getErrorMessage } from "@/utils/handleError";
import {
  computed,
  defineComponent,
  onMounted,
  reactive,
  ref,
  toRaw,
  watch,
} from "@vue/runtime-core";
import {
  DeleteOutlined,
  PlusOutlined,
  InfoCircleOutlined,
} from "@ant-design/icons-vue";
import { notification } from "ant-design-vue";
import { useRoute, useRouter } from "vue-router";
import * as dayjs from "dayjs";
import InputMoney from "@/components/_commons/InputMoney.vue";
import ContestBankAccountForm from "@/components/Contests/Admin/BankAccountsForm.vue";
import ContestGalleryForm from "@/components/Contests/Admin/GalleryForm.vue";
import { Rule } from "ant-design-vue/lib/form";

export default defineComponent({
  name: "AdminContestDetail",
  components: {
    InputMoney,
    ContestBankAccountForm,
    ContestGalleryForm,
    InfoCircleOutlined,
    DeleteOutlined,
    PlusOutlined,
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    const { id } = route.params;
    const contest = ref<ContestDetail>();
    const loading = ref<boolean>(false);
    const gallery = ref<string[]>([]);
    const bankAccounts = ref<number[]>([]);
    const formState = reactive<ContestEditRequest>({
      contest_date: "",
      max_reserve_days: 1,
      short_description: "",
      whatsapp_number: "",
      whatsapp_group: "",
      sales: [],
      bank_accounts: [],
      gallery: [],
    });

    const pageTitle = computed(() => {
      if (contest.value === undefined) return "Carregando...";

      const { title } = contest.value;

      return loading.value ? "Carregando..." : title;
    });

    async function getContestDetails() {
      try {
        loading.value = true;
        contest.value = await getContest(Number(id));
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    async function handleSubmit(values: ContestEditRequest) {
      try {
        loading.value = true;
        const obj: ContestEditRequest = {
          ...values,
          gallery: toRaw(gallery.value),
          bank_accounts: toRaw(values.bank_accounts),
        };

        const result = await editContest(Number(id), obj);

        notification.success({
          message: result.message,
        });

        router.push("/admin/sorteios");
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    function handleSelectAccount(accounts: number[]) {
      const values = Object.entries(accounts).map(
        (value) => value[1] as number
      );

      formState.bank_accounts = [...values];
    }

    function handleChangeImages(images: string[]) {
      gallery.value = images;
    }

    function disabledDate(current: dayjs.Dayjs) {
      return current && current < dayjs().endOf("day");
    }

    function addSale() {
      formState.sales.push({
        quantity: 1,
        price: 0.1,
      } as ContestSale);
    }

    function removeSale(sale: any) {
      let index = formState.sales.indexOf(sale as ContestSale);

      if (index !== -1) {
        formState.sales.splice(index, 1);
      }
    }

    const salePriceValidator = async (_rule: Rule, value: string) => {
      if (value === "") {
        return Promise.reject("Campo obrigatório!");
      } else {
        formState.sales.forEach((sale) => {
          if (sale.price < 0.1 || sale.price === null) {
            return Promise.reject("O preço deve ser maior que R$ 0,10");
          }
        });

        return Promise.resolve();
      }
    };

    watch(contest, (newVal) => {
      formState.contest_date = newVal.contest_date;
      formState.max_reserve_days = newVal.max_reserve_days;
      formState.short_description = newVal.short_description;
      formState.whatsapp_number = newVal.whatsapp_number;
      formState.whatsapp_group = newVal.whatsapp_group;
      formState.sales = toRaw(newVal.sales);
      formState.bank_accounts = newVal.bank_accounts.map((acc) => acc.id);
      formState.gallery = newVal.gallery.map((img) => img.path);

      gallery.value = newVal.gallery.map((img) => img.path);
      bankAccounts.value = newVal.bank_accounts.map((acc) => acc.id);
    });

    onMounted(async () => {
      await getContestDetails();
    });

    return {
      contest,
      loading,
      pageTitle,
      formState,
      gallery,
      bankAccounts,
      handleSubmit,
      handleSelectAccount,
      handleChangeImages,
      disabledDate,
      addSale,
      removeSale,
      salePriceValidator,
    };
  },
});
</script>

<style>
</style>