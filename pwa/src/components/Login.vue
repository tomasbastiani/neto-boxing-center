<template>
    <div class="container mt-5">
      <b-card title="Inicio de Sesión" class="mx-auto" style="max-width: 400px;">
        <b-form @submit.prevent="login">
          <b-form-group label="Usuario" label-for="username">
            <b-form-input
              id="username"
              v-model="username"
              type="text"
              placeholder="Ingrese su usuario"
              required
            ></b-form-input>
          </b-form-group>
  
          <b-form-group label="Contraseña" label-for="password">
            <b-form-input
              id="password"
              v-model="password"
              type="password"
              placeholder="Ingrese su contraseña"
              required
            ></b-form-input>
          </b-form-group>
  
          <b-button type="submit" variant="primary" block>
            Iniciar Sesión
          </b-button>
        </b-form>
      </b-card>
    </div>
  </template>
  
  <script>
  export default {
    name: "LoginComponent",
    data() {
      return {
        username: '',
        password: '',
      };
    },
    methods: {
      login() {
        // Lógica de inicio de sesión aquí
        console.log('Iniciar Sesión con:', this.username, this.password);
        // Puedes agregar tu lógica de autenticación aquí
      },
    },
  };
  </script>
  
  <style scoped>
  /* Estilos específicos del componente si es necesario */
  </style>
  