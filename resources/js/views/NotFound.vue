<template>
  <a-result status="404" title="Ops! Página não encontrada"
    sub-title="Desculpe, a página que você tentou acessar não está mais disponível.">
    <template #extra>
      <router-link to="/">
        <a-button type="primary">Voltar para a home</a-button>
      </router-link>
    </template>
  </a-result>
</template>
