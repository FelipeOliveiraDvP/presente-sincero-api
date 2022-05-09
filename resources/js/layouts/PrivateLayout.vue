<template>
  <div class="private-layout">
    <header class="private-header">
      <div class="logo">LOGO</div>

      <div class="user-info">
        <span>
          <b-avatar
            src="https://picsum.photos/600/300/"
            size="2.5rem"
          ></b-avatar>
          <span>Usuário</span>
        </span>
        <b-button variant="outline-danger"
          ><i class="fas fa-sign-out-alt"></i> Sair</b-button
        >
      </div>
    </header>
    <div class="private-wrapper">
      <aside class="menu">
        <nav>
          <ul>
            <li>
              <i class="fas fa-trophy"></i> Sorteios
              <ul>
                <li>
                  <router-link to="/admin/sorteios"> Ver todos </router-link>
                </li>
                <li>
                  <router-link to="/admin/sorteios/novo-sorteio">
                    Novo sorteio
                  </router-link>
                </li>
              </ul>
            </li>
            <li>
              <router-link to="/admin/clientes">
                <i class="fas fa-users"></i> Clientes
              </router-link>
            </li>
          </ul>
        </nav>
      </aside>
      <main class="content">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script>
export default {
  name: "PrivateLayout",
};
</script>

<style>
</style>