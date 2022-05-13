<template>
  <b-card no-body class="overflow-hidden p-2 mb-3" style="max-width: 540px">
    <b-row no-gutters>
      <b-col md="4" class="d-flex justify-content-center align-items-center">
        <b-avatar
          size="6rem"
          variant="success"
          src="http://lorempixel.com.br/600/600"
        ></b-avatar>
      </b-col>
      <b-col md="8">
        <b-card-body title="João da Silva" class="text-center text-md-left">
          <b-card-text> Ganhou o sorteio nº 1354 </b-card-text>
        </b-card-body>
      </b-col>
    </b-row>
  </b-card>
</template>

<script>
export default {
  name: "WinnerCard",
};
</script>

<style>
</style>