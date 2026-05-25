<template>
  <div class="kiosk-shell">
    <!-- Reloj digital en la parte superior -->
    <header class="kiosk-header">
      <div class="kiosk-logo-container">
        <img src="https://i.postimg.cc/4dfPpWvJ/logoneto.png" alt="Logo Neto" class="kiosk-logo" />
        <span class="kiosk-logo-text">NETO BOXING CENTER</span>
      </div>
      <div class="kiosk-clock">
        <span class="time">{{ timeString }}</span>
        <span class="date">{{ dateString }}</span>
      </div>
      <router-link to="/admin" class="btn-exit-kiosk" title="Salir del Modo Kiosco">
        <span>⚙️ Admin</span>
      </router-link>
    </header>

    <!-- Contenedor principal de ingreso -->
    <main class="kiosk-main">
      <div class="kiosk-card glass">
        <h2>CONTROL DE ACCESO</h2>
        <p class="subtitle">Escriba su DNI y presione <kbd>Enter</kbd></p>

        <form @submit.prevent="buscarPorDNI" class="kiosk-form">
          <div class="input-wrapper">
            <input
              ref="dniInput"
              v-model="searchDNI"
              type="text"
              pattern="[0-9]*"
              inputmode="numeric"
              placeholder="INGRESAR DNI"
              class="kiosk-input"
              :disabled="loading"
              autocomplete="off"
            />
            <span class="focus-indicator"></span>
          </div>

          <button type="submit" class="btn-kiosk-submit" :disabled="loading || !searchDNI">
            <span v-if="loading" class="spinner"></span>
            <span v-else>Validar Acceso</span>
          </button>
        </form>
      </div>
    </main>

    <!-- Overlay de pantalla completa para estado de acceso -->
    <transition name="fade">
      <div
        v-if="status"
        :class="['status-overlay', status === 'activo' ? 'overlay-success' : 'overlay-danger']"
        @click="dismissStatus"
      >
        <div class="status-content">
          <!-- Icono enorme -->
          <div class="status-icon-ring">
            <span class="status-icon" v-if="status === 'activo'">✓</span>
            <span class="status-icon" v-else>✗</span>
          </div>

          <!-- Mensajes de estado -->
          <template v-if="status === 'activo' && socioSeleccionado">
            <h1 class="status-title">ACCESO PERMITIDO</h1>
            <p class="welcome-text">¡Bienvenido/a!</p>
            <h2 class="member-name">{{ socioSeleccionado.nombre }} {{ socioSeleccionado.apellido }}</h2>
            <div class="status-details">
              <p>Sede: <strong>{{ socioSeleccionado.sede }}</strong></p>
              <p class="expiration-info">
                Su cuota vence en 
                <span class="days-badge">{{ calcularDiasParaVencimiento(socioSeleccionado.expiration) }} días</span>
                ({{ formatFecha(socioSeleccionado.expiration) }})
              </p>
            </div>
          </template>

          <template v-else-if="status === 'vencido' && socioSeleccionado">
            <h1 class="status-title">ACCESO DENEGADO</h1>
            <p class="welcome-text">Cuota Vencida</p>
            <h2 class="member-name">{{ socioSeleccionado.nombre }} {{ socioSeleccionado.apellido }}</h2>
            <div class="status-details">
              <p>Venció el: <strong class="date-expired">{{ formatFecha(socioSeleccionado.expiration) }}</strong></p>
              <p class="action-alert">Por favor, pase por administración para regularizar su estado.</p>
            </div>
          </template>

          <template v-else-if="status === 'no_existe'">
            <h1 class="status-title">ACCESO DENEGADO</h1>
            <h2 class="member-name">DNI NO REGISTRADO</h2>
            <div class="status-details">
              <p class="action-alert">El DNI ingresado no se encuentra en el sistema.</p>
              <p class="action-alert-sub">Por favor, diríjase a recepción para registrarse.</p>
            </div>
          </template>

          <template v-else-if="status === 'error'">
            <h1 class="status-title">ERROR DE SISTEMA</h1>
            <h2 class="member-name">FALLO DE CONEXIÓN</h2>
            <div class="status-details">
              <p class="action-alert">No se pudo verificar el estado en este momento.</p>
              <p class="action-alert-sub">Por favor intente nuevamente o consulte al personal.</p>
            </div>
          </template>

          <span class="dismiss-hint">Toque en cualquier parte para continuar</span>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from "axios";
import moment from "moment";

export default {
  name: "AccesoComponent",
  data() {
    return {
      searchDNI: "",
      loading: false,
      status: null, // 'activo', 'vencido', 'no_existe', 'error'
      socioSeleccionado: null,
      timeString: "",
      dateString: "",
      clockInterval: null,
      timeoutId: null,
    };
  },
  mounted() {
    this.updateClock();
    this.clockInterval = setInterval(this.updateClock, 1000);
    this.focusInput();
    // Bloquear el foco en el input: cualquier click en la pantalla redirige el cursor al input
    document.addEventListener("click", this.focusInput);
  },
  beforeDestroy() {
    if (this.clockInterval) clearInterval(this.clockInterval);
    if (this.timeoutId) clearTimeout(this.timeoutId);
    document.removeEventListener("click", this.focusInput);
  },
  methods: {
    updateClock() {
      const now = moment();
      this.timeString = now.format("HH:mm:ss");
      // Formato: Lunes, 24 de Mayo de 2026
      this.dateString = now.locale("es").format("dddd, D [de] MMMM [de] YYYY");
    },
    focusInput() {
      // Solo hacer focus si no hay un overlay activo
      if (this.status) return;
      const input = this.$refs.dniInput;
      if (input) {
        input.focus();
      }
    },
    async buscarPorDNI() {
      if (!this.searchDNI || this.loading) return;
      this.loading = true;

      try {
        const apiUrl = process.env.VUE_APP_BACKEND_API_URL;
        // Obtener la lista actualizada de socios para validar en tiempo real
        const response = await axios.get(`${apiUrl}socios/get`);
        const socios = response.data.socios;
        
        const socioEncontrado = socios.find(s => s.dni.trim() === this.searchDNI.trim());

        if (!socioEncontrado) {
          this.showStatus("no_existe", null);
          return;
        }

        // Validar vencimiento
        const isActive = new Date(socioEncontrado.expiration) > new Date();
        this.showStatus(isActive ? "activo" : "vencido", socioEncontrado);

        // Registrar el ingreso en la base de datos de manera asíncrona
        const ingresoData = {
          dni: socioEncontrado.dni,
          nombre: socioEncontrado.nombre,
          apellido: socioEncontrado.apellido,
          sede: socioEncontrado.sede,
          last_pay: socioEncontrado.last_pay,
          expiration: socioEncontrado.expiration,
          fecha_ingreso: new Date().toISOString()
        };

        axios.post(`${apiUrl}socios/ingresos`, ingresoData)
          .catch(err => console.error("Error al registrar ingreso en backend:", err));

      } catch (error) {
        console.error("Error validando el DNI:", error);
        this.showStatus("error", null);
      } finally {
        this.loading = false;
      }
    },
    showStatus(statusType, socio) {
      this.status = statusType;
      this.socioSeleccionado = socio;

      // Reproducir sonido de validación
      if (statusType === "activo") {
        this.playBeep("success");
      } else {
        this.playBeep("error");
      }

      // Limpiar input de búsqueda inmediatamente
      this.searchDNI = "";

      // Auto-ocultar overlay después de un tiempo
      if (this.timeoutId) clearTimeout(this.timeoutId);
      this.timeoutId = setTimeout(() => {
        this.dismissStatus();
      }, statusType === "activo" ? 4000 : 5000);
    },
    dismissStatus() {
      this.status = null;
      this.socioSeleccionado = null;
      if (this.timeoutId) clearTimeout(this.timeoutId);
      this.$nextTick(() => {
        this.focusInput();
      });
    },
    calcularDiasParaVencimiento(fechaExpiracion) {
      const expiracion = moment(fechaExpiracion).startOf("day");
      const hoy = moment().startOf("day");
      return expiracion.diff(hoy, "days");
    },
    formatFecha(fecha) {
      return moment(fecha).format("DD/MM/YYYY");
    },
    playBeep(type) {
      try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        const ctx = new AudioContext();

        if (type === "success") {
          // Sonido de éxito: Doble bip agudo y alegre (bip-bip)
          const osc1 = ctx.createOscillator();
          const gain1 = ctx.createGain();
          osc1.type = "sine";
          osc1.frequency.setValueAtTime(880, ctx.currentTime); // La5 (880Hz)
          osc1.connect(gain1);
          gain1.connect(ctx.destination);
          gain1.gain.setValueAtTime(0.15, ctx.currentTime);
          gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
          osc1.start();
          osc1.stop(ctx.currentTime + 0.1);

          setTimeout(() => {
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = "sine";
            osc2.frequency.setValueAtTime(1109.73, ctx.currentTime); // Do#6 (1110Hz)
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            gain2.gain.setValueAtTime(0.15, ctx.currentTime);
            gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);
            osc2.start();
            osc2.stop(ctx.currentTime + 0.15);
          }, 100);
        } else {
          // Sonido de fallo / vencido: Zumbido grave y largo
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          osc.type = "sawtooth"; // Ondas de sierra para un tono más robótico/de alerta
          osc.frequency.setValueAtTime(140, ctx.currentTime); // Frecuencia baja
          osc.connect(gain);
          gain.connect(ctx.destination);
          gain.gain.setValueAtTime(0.2, ctx.currentTime);
          gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.6);
          osc.start();
          osc.stop(ctx.currentTime + 0.6);
        }
      } catch (error) {
        console.warn("Fallo al reproducir audio:", error);
      }
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap');

.kiosk-shell {
  min-height: 100vh;
  background: #040507;
  color: #e6e9ef;
  font-family: 'Outfit', sans-serif;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
}

/* ── Cabecera Kiosco ── */
.kiosk-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 40px;
  background: rgba(15, 17, 21, 0.85);
  border-bottom: 1px solid #232a3a;
  backdrop-filter: blur(10px);
  z-index: 10;
}

.kiosk-logo-container {
  display: flex;
  align-items: center;
  gap: 15px;
}

.kiosk-logo {
  height: 55px;
  filter: drop-shadow(0 0 10px rgba(206, 216, 0, 0.15));
}

.kiosk-logo-text {
  font-size: 20px;
  font-weight: 800;
  letter-spacing: 1px;
  color: #fff;
  background: linear-gradient(135deg, #fff 0%, #a8b3cf 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.kiosk-clock {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.kiosk-clock .time {
  font-size: 32px;
  font-weight: 900;
  color: #CED800;
  letter-spacing: 1px;
  text-shadow: 0 0 15px rgba(206, 216, 0, 0.3);
}

.kiosk-clock .date {
  font-size: 13px;
  font-weight: 500;
  color: #a8b3cf;
  text-transform: capitalize;
  margin-top: -2px;
}

.btn-exit-kiosk {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #a8b3cf;
  padding: 10px 18px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-exit-kiosk:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  border-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-1px);
}

/* ── Contenido Principal ── */
.kiosk-main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  z-index: 5;
  background: radial-gradient(circle at center, rgba(206, 216, 0, 0.02) 0%, transparent 70%);
}

.kiosk-card {
  width: 100%;
  max-width: 580px;
  border-radius: 28px;
  padding: 60px 40px;
  text-align: center;
  box-shadow: 
    0 30px 60px rgba(0, 0, 0, 0.6),
    0 0 0 1px rgba(206, 216, 0, 0.05) inset;
  position: relative;
}

.glass {
  background: rgba(15, 17, 21, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(20px) saturate(1.2);
}

.kiosk-card h2 {
  font-size: 32px;
  font-weight: 900;
  margin: 0 0 10px 0;
  letter-spacing: 1px;
  color: #fff;
}

.kiosk-card .subtitle {
  color: #a8b3cf;
  font-size: 16px;
  margin: 0 0 45px 0;
}

.kiosk-card .subtitle kbd {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 6px;
  padding: 3px 8px;
  font-size: 13px;
  font-family: inherit;
  color: #fff;
}

.kiosk-form {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.input-wrapper {
  position: relative;
  width: 100%;
}

.kiosk-input {
  width: 100%;
  height: 90px;
  background: rgba(4, 5, 7, 0.85);
  border: 2px solid #232a3a;
  border-radius: 20px;
  color: #CED800;
  font-size: 42px;
  font-weight: 800;
  letter-spacing: 4px;
  text-align: center;
  outline: none;
  transition: all 0.25s ease;
  box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.4);
}

.kiosk-input:focus {
  border-color: #CED800;
  box-shadow: 
    0 0 0 6px rgba(206, 216, 0, 0.15),
    inset 0 4px 10px rgba(0, 0, 0, 0.4);
  background: #000;
}

.kiosk-input::placeholder {
  color: rgba(168, 179, 207, 0.25);
  letter-spacing: 1px;
  font-size: 26px;
  font-weight: 600;
}

.btn-kiosk-submit {
  height: 65px;
  border-radius: 18px;
  border: none;
  background: #CED800;
  color: #040507;
  font-size: 18px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 10px 25px rgba(206, 216, 0, 0.2);
}

.btn-kiosk-submit:hover:not(:disabled) {
  background: #f9ff79;
  transform: translateY(-2px);
  box-shadow: 0 15px 30px rgba(206, 216, 0, 0.3);
}

.btn-kiosk-submit:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  box-shadow: none;
}

/* Spinner animado */
.spinner {
  display: inline-block;
  width: 25px;
  height: 25px;
  border: 3px solid rgba(4, 5, 7, 0.2);
  border-radius: 50%;
  border-top-color: #040507;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ── Overlays de Estado a Pantalla Completa ── */
.status-overlay {
  position: fixed;
  inset: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 20px;
  cursor: pointer;
  animation: scaleUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes scaleUp {
  from { transform: scale(1.05); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.overlay-success {
  background: linear-gradient(135deg, #0d381e 0%, #051d0e 100%);
  border: 5px solid #22c55e;
}

.overlay-danger {
  background: linear-gradient(135deg, #4c0b0b 0%, #2a0505 100%);
  border: 5px solid #ef4444;
}

.status-content {
  max-width: 900px;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  animation: contentSlide 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
}

@keyframes contentSlide {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.status-icon-ring {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 35px;
}

.overlay-success .status-icon-ring {
  background: rgba(34, 197, 94, 0.1);
  border: 4px solid #22c55e;
  box-shadow: 0 0 30px rgba(34, 197, 94, 0.4);
}

.overlay-danger .status-icon-ring {
  background: rgba(239, 68, 68, 0.1);
  border: 4px solid #ef4444;
  box-shadow: 0 0 30px rgba(239, 68, 68, 0.4);
}

.status-icon {
  font-size: 70px;
  font-weight: 800;
  line-height: 1;
}

.overlay-success .status-icon {
  color: #22c55e;
}

.overlay-danger .status-icon {
  color: #ef4444;
}

.status-title {
  font-size: 65px;
  font-weight: 900;
  letter-spacing: 2px;
  margin: 0;
  line-height: 1.1;
}

.overlay-success .status-title {
  color: #22c55e;
  text-shadow: 0 0 25px rgba(34, 197, 94, 0.4);
}

.overlay-danger .status-title {
  color: #ef4444;
  text-shadow: 0 0 25px rgba(239, 68, 68, 0.4);
}

.welcome-text {
  font-size: 26px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.7);
  margin: 15px 0 0 0;
  letter-spacing: 1px;
}

.member-name {
  font-size: 58px;
  font-weight: 900;
  color: #fff;
  margin: 15px 0 35px 0;
  letter-spacing: 1px;
}

.status-details {
  background: rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(255, 255, 255, 0.05);
  padding: 25px 45px;
  border-radius: 20px;
  font-size: 22px;
  color: #e6e9ef;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.status-details p {
  margin: 0;
}

.status-details strong {
  color: #fff;
  font-weight: 700;
}

.days-badge {
  background: rgba(34, 197, 94, 0.2);
  border: 1px solid rgba(34, 197, 94, 0.3);
  padding: 4px 12px;
  border-radius: 8px;
  color: #86efac;
  font-weight: 800;
  margin: 0 4px;
}

.date-expired {
  color: #fca5a5 !important;
  text-decoration: underline;
}

.action-alert {
  color: #ff9e9e;
  font-weight: 700;
  font-size: 24px;
}

.action-alert-sub {
  color: rgba(255, 255, 255, 0.6);
  font-size: 18px;
  margin-top: -5px !important;
}

.dismiss-hint {
  margin-top: 50px;
  font-size: 16px;
  color: rgba(255, 255, 255, 0.35);
  letter-spacing: 1px;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 0.35; }
  50% { opacity: 0.7; }
}

/* Transición Fade */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

/* ── Ajustes de pantalla responsivos ── */
@media (max-width: 768px) {
  .kiosk-header {
    padding: 15px 20px;
  }
  .kiosk-logo-text {
    display: none;
  }
  .kiosk-clock .time {
    font-size: 24px;
  }
  .kiosk-card {
    padding: 40px 20px;
  }
  .kiosk-card h2 {
    font-size: 26px;
  }
  .kiosk-input {
    height: 75px;
    font-size: 32px;
  }
  .status-title {
    font-size: 40px;
  }
  .member-name {
    font-size: 38px;
  }
  .status-details {
    padding: 15px 25px;
    font-size: 16px;
  }
  .action-alert {
    font-size: 18px;
  }
}
</style>
