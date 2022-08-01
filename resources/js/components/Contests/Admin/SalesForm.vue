<template>
  <div>Form de promoções</div>
  <!-- <div class="mt-2">
    <h3>Promoções</h3>

    <b-row v-for="(sale, index) in contest.sales" :key="`sale-${index}`">
      <b-col sm="12" md="4">
        <b-form-group label="Números" label-for="sale_quantity">
          <b-form-input
            id="sale_quantity"
            v-model="sale.quantity"
            type="number"
          ></b-form-input>
        </b-form-group>
      </b-col>
      <b-col cols="10" md="6">
        <b-form-group id="sale_price" label="Valor R$" label-for="sale_price">
          <the-mask
            :mask="['#,##', '##,##', '###,##', '#.###,##']"
            class="form-control"
            id="sale_price"
            v-model="sale.price"
          ></the-mask>
        </b-form-group>
      </b-col>
      <b-col cols="2" md="2" class="d-flex align-items-end">
        <b-button variant="danger" @click="removeSale(index)">
          <font-awesome-icon icon="fa-solid fa-trash" />
        </b-button>
      </b-col>
    </b-row>

    <b-button variant="primary" class="my-4" @click="addSale"
      >Nova promoção</b-button
    >
  </div> -->
</template>

<script>
export default {
  props: {
    v: Object,
    contest: Object,
    addSale: Function,
    removeSale: Function,
  },
  name: "SalesForm",
};
</script>

<style>
</style>