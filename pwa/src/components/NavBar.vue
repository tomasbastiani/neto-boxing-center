<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-custom">
    <div class="container">
      <!-- Logo a la izquierda -->
      <router-link to="/" class="navbar-brand">
        <img src="https://i.postimg.cc/4dfPpWvJ/logoneto.png" alt="Logo" height="50">
      </router-link>

      <!-- Botón de hamburguesa para dispositivos móviles -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Elementos de la barra de navegación -->
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <!-- Estilos personalizados para los elementos de navegación -->
          <li class="nav-item">
            <router-link to="/" class="nav-link text-light">Inicio</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/about" class="nav-link text-light">Acerca de</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/services" class="nav-link text-light">Servicios</router-link>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="https://api.whatsapp.com/send?phone=1150524048" target="_blank">Contactanos</a>
          </li>
        </ul>
      </div>
      <!-- Botón de acceso -->
      <b-button @click="toLogin" class="btn btn-outline-secondary btn-sm">Acceder</b-button>
    </div>
  </nav>
</template>

<script>
// import LoginComponent from './intranet/LoginComponent.vue';

export default {
  name: 'NavBar',
  created() {
  },
  methods: {
    toLogin() {
        // this.$router.push({ LoginComponent });
        let currentRoute = this.$route.path;
        if (currentRoute !== '/login') {
        // Realizar la navegación solo si no estás en la misma ruta
        this.$router.push({ path: '/login' });
      }
    }
  },
};
</script>

<style scoped>
/* Importa el archivo CSS de Bootstrap */
@import '~bootstrap/dist/css/bootstrap.min.css';

/* Color de fondo personalizado */
.bg-custom {
  background-color: rgba(4, 5, 7, 1) !important;
}
</style>
