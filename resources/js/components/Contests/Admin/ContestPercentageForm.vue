<template>
  <a-typography-title :level="3"> Progresso </a-typography-title>
  <a-typography-text type="secondary">
    Controle a exibição do progresso do sorteio.
  </a-typography-text>  
  <br />
  <a-radio-group
    v-model:value="selectedMode"
    style="margin-top: 8px"
    @change="handleChangePercentage"
  >
    <a-radio-button value="hidden">Esconder</a-radio-button>
    <a-radio-button value="show_true">Valor atual</a-radio-button>
    <a-radio-button value="show_custom">Customizada</a-radio-button>
  </a-radio-group>
  <br />
  <br />
  <a-space direction="vertical">
    <a-form-item v-if="selectedMode === 'hidden'">
      <a-typography-title :level="4"> Esconder porcentagem </a-typography-title>
      <a-typography-paragraph>
        Ao selecionar essa opção, a barra de progresso do sorteio torna-se
        oculta.
      </a-typography-paragraph>
    </a-form-item>

    <a-form-item v-if="selectedMode === 'show_true'">
      <a-typography-title :level="4"> Porcentagem atual </a-typography-title>
      <a-typography-paragraph>
        Ao selecionar essa opção, a barra de progresso do sorteio vai exibir a
        porcentagem real de números que já foram comprados pelos clientes.
      </a-typography-paragraph>
    </a-form-item>

    <a-form-item v-if="selectedMode === 'show_custom'">
      <a-typography-title :level="4">
        Porcentagem customizada
      </a-typography-title>
      <a-typography-paragraph>
        Ao selecionar essa opção, você pode selecionar a porcentagem que que vai
        ser exibida para os clientes no site. (Esse valor não vai afetar a
        porcentagem real do sorteio)
      </a-typography-paragraph>

      <a-slider
        v-model:value="changePercentage.custom_percentage"
        @afterChange="handleChangePercentage"
      />
    </a-form-item>
  </a-space>
</template>

<script lang="ts">
import { editContest } from "@/services/contests";
import { ContestDetail, ContestEditRequest } from "@/types/Contest.types";
import { getErrorMessage } from "@/utils/handleError";
import {
  defineComponent,
  PropType,
  reactive,
  ref,
  toRefs,
  watch,
} from "@vue/runtime-core";
import { notification } from "ant-design-vue";

type PercentageType = "hidden" | "show_custom" | "show_true";

export default defineComponent({
  name: "ContestPercentageForm",
  props: {
    contestId: {
      type: Number,
      required: true,
    },
    contest: {
      type: Object as PropType<ContestDetail>,
    },
  },
  setup(props) {
    const { contestId, contest } = toRefs(props);
    const selectedMode = ref<PercentageType>("hidden");
    const changePercentage = reactive<ContestEditRequest>({
      show_percentage: false,
      use_custom_percentage: false,
      custom_percentage: 0,
    });

    async function handleChangePercentage() {
      try {
        if (selectedMode.value === "hidden") {
          changePercentage.show_percentage = false;
          changePercentage.use_custom_percentage = false;
        }

        if (selectedMode.value === "show_true") {
          changePercentage.show_percentage = true;
          changePercentage.use_custom_percentage = false;
        }

        if (selectedMode.value === "show_custom") {
          changePercentage.show_percentage = false;
          changePercentage.use_custom_percentage = true;
        }

        const result = await editContest(contestId.value, {
          ...changePercentage,
          custom_percentage: changePercentage.custom_percentage / 100,
        });

        notification.success({
          message: result.message,
        });
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      }
    }

    watch(contest, (newVal) => {
      const { show_percentage, use_custom_percentage, custom_percentage } =
        newVal;

      if (show_percentage) {
        selectedMode.value = "show_true";
        changePercentage.show_percentage = true;
        changePercentage.use_custom_percentage = false;
      } else if (use_custom_percentage) {
        selectedMode.value = "show_custom";
        changePercentage.show_percentage = false;
        changePercentage.use_custom_percentage = true;
      } else {
        selectedMode.value = "hidden";
        changePercentage.show_percentage = false;
        changePercentage.use_custom_percentage = false;
      }

      changePercentage.custom_percentage = custom_percentage * 100;
    });

    return {
      selectedMode,
      changePercentage,
      handleChangePercentage,
    };
  },
});
</script>

<style>
</style>