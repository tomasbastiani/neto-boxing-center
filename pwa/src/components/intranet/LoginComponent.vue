<template>
  <div class="login-wrapper">
    <div class="login-container glass">
      <div class="brand-header">
        <img src="https://i.postimg.cc/4dfPpWvJ/logoneto.png" alt="Logo" class="login-logo" />
        <h2 class="login-title">Administración</h2>
        <p class="login-subtitle">Acceso al panel interno</p>
      </div>

      <form @submit.prevent="login" class="login-form">
        <!-- Campo de usuario -->
        <div class="form-group-custom">
          <label for="username">Usuario</label>
          <input 
            type="text" 
            id="username" 
            v-model="username" 
            placeholder="Ingrese su usuario" 
            required 
            :disabled="loading"
            class="form-input"
          />
        </div>

        <!-- Campo de contraseña -->
        <div class="form-group-custom">
          <label for="password">Contraseña</label>
          <input 
            type="password" 
            id="password" 
            v-model="password" 
            placeholder="Ingrese su contraseña" 
            required 
            :disabled="loading"
            class="form-input"
          />
        </div>

        <!-- Mensaje de error -->
        <transition name="fade">
          <div v-if="errorMessage" class="error-banner">
            Credenciales inválidas. Intente nuevamente.
          </div>
        </transition>

        <!-- Botón de iniciar sesión -->
        <button 
          type="submit" 
          class="btn-login-submit" 
          :disabled="loading"
        >
          <span v-if="loading" class="spinner-custom"></span>
          <span v-else>Iniciar Sesión</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginComponent',
  data() {
    return {
      username: '',
      password: '',
      errorMessage: false,
      loading: false
    };
  },
  methods: {
    login() {
      this.errorMessage = false;
      this.loading = true;

      // Datos de inicio de sesión
      const credentials = {
        username: this.username,
        password: this.password,
      };

      const apiUrl = process.env.VUE_APP_BACKEND_API_URL;
      axios.post(`${apiUrl}login`, credentials)
        .then(response => {
          console.log(response.data);
          sessionStorage.setItem('user', this.username);
          this.$router.push({ path: '/admin' });
        })
        .catch(error => {
          this.errorMessage = true;
          if (error.response) {
            console.error('Error de respuesta:', error.response.data);
          } else {
            console.error('Error al enviar la solicitud:', error.message);
          }
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap');

.login-wrapper {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #040507;
  padding: 60px 20px;
  font-family: 'Outfit', sans-serif;
}

.login-container {
  width: 100%;
  max-width: 420px;
  background: rgba(21, 25, 34, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 24px;
  padding: 40px 30px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(206, 216, 0, 0.06) inset;
  position: relative;
  overflow: hidden;
}

.glass {
  backdrop-filter: blur(12px) saturate(1.2);
}

.brand-header {
  text-align: center;
  margin-bottom: 35px;
}

.login-logo {
  height: 80px;
  margin-bottom: 15px;
  filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));
}

.login-title {
  color: #fff;
  font-size: 24px;
  font-weight: 700;
  margin: 0;
  letter-spacing: 0.5px;
}

.login-subtitle {
  color: #a8b3cf;
  font-size: 14px;
  margin: 5px 0 0 0;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group-custom {
  display: flex;
  flex-direction: column;
  text-align: left;
  gap: 8px;
}

.form-group-custom label {
  color: #a8b3cf;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.form-input {
  width: 100%;
  height: 50px;
  background: #0b0e14;
  border: 1px solid #2a3040;
  border-radius: 12px;
  color: #fff;
  padding: 0 16px;
  font-size: 15px;
  outline: none;
  transition: all 0.2s ease;
}

.form-input:focus {
  border-color: #CED800;
  box-shadow: 0 0 0 4px rgba(206, 216, 0, 0.15);
}

.form-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-banner {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: #fca5a5;
  padding: 12px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  text-align: center;
}

.btn-login-submit {
  width: 100%;
  height: 52px;
  background: #CED800;
  color: #040507;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-login-submit:hover:not(:disabled) {
  background: #f9ff79;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(206, 216, 0, 0.25);
}

.btn-login-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Spinner animado */
.spinner-custom {
  width: 22px;
  height: 22px;
  border: 3px solid rgba(4, 5, 7, 0.2);
  border-radius: 50%;
  border-top-color: #040507;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
