<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Iniciar Sesión</h5>
            <form>
              <!-- Campo de usuario -->
              <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="username" v-model="username" placeholder="Ingrese su usuario">
              </div>

              <!-- Campo de contraseña -->
              <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" v-model="password" placeholder="Ingrese su contraseña">
              </div>

              <!-- Botón de iniciar sesión -->
              <button type="button" class="btn btn-primary" @click="login">Iniciar Sesión</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
// import {route} from "../../environment.js"

export default {
  name: 'LoginComponent',
  data() {
    return {
      username: '',
      password: '',
    };
  },
  methods: {
    login() {
      // Datos de inicio de sesión
      const credentials = {
        username: this.username,
        password: this.password
      };

      axios.post('http://localhost:8080/api/login', credentials)
        .then(response => {
          let currentRoute = this.$route.path;
          // Manejar la respuesta del servidor aquí
          console.log(response.data);
          if (currentRoute !== '/admin') {
            this.$router.push({ path: '/admin' });
          }
        })
        .catch(error => {
          // Manejar errores de la solicitud aquí
          if (error.response) {
            // La solicitud fue hecha, pero el servidor respondió con un código de estado fuera del rango 2xx
            console.error('Error de respuesta:', error.response.data);
            console.error('Código de estado HTTP:', error.response.status);
          } else if (error.request) {
            // La solicitud fue hecha, pero no se recibió respuesta
            console.error('No se recibió respuesta del servidor:', error.request);
          } else {
            // Ocurrió un error antes de enviar la solicitud
            console.error('Error al enviar la solicitud:', error.message);
          }
          console.error('Error detallado:', error.config);
        });
    }
  },
};
</script>

<style scoped>
.card{
  margin-bottom: 100px;
  margin-top: 50px;
}
</style>
