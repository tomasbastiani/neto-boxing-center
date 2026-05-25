<template>
  <div id="container" class="app-shell">
    <!-- Header -->
    <header class="app-header">
      <div class="brand">
        <span class="brand-dot"></span>
        <h1>Administración de Socios</h1>
      </div>
      <div class="header-actions">
        <button class="btn btn-success btn-modern" @click="mostrarModalCreate = true">+ Nuevo Socio</button>
        <button class="btn btn-primary btn-modern" @click="goToIngresos">Ingresos</button>
        <button class="btn btn-modern" style="color: #CED800; border-color: rgba(206,216,0,.25);" @click="goToAcceso">🖥️ Pantalla Acceso</button>
        <b-button @click="logout" class="btn btn-danger btn-modern btn-sm">Cerrar Sesión</b-button>
      </div>
    </header>

    <!-- Toasts (mantienen tus mismos IDs y funciones) -->
    <div class="toast-stack">
      <div id="mensajeError" class="toast-item toast-error" style="display:none;">Ocurrió un error.</div>
      <div id="mensajeErrorDNI" class="toast-item toast-error" style="display:none;">El DNI ya se encuentra registrado.</div>
      <div id="mensajeDelete" class="toast-item toast-success" style="display:none;">¡Socio eliminado con éxito!</div>
      <div id="mensajeExito" class="toast-item toast-success" style="display:none;">¡Socio creado con éxito!</div>
      <div id="mensajeUpdate" class="toast-item toast-info" style="display:none;">Socio actualizado con éxito.</div>
      <div id="mensajeWhatsapp" class="toast-item toast-success" style="display:none;">¡Mensaje de WhatsApp enviado!</div>
      <div id="mensajeErrorWhatsapp" class="toast-item toast-error" style="display:none;">Error al enviar WhatsApp. (Revisa servidor/código)</div>
    </div>

    <!-- Acceso Socio -->
    <section id="busquedaSocio" class="access-card">
      <h2 class="section-title">Acceso Socio</h2>
      <p class="section-subtitle">Ingrese DNI y presione <kbd>Enter</kbd></p>

      <div class="access-inputs">
        <input
          v-focus
          v-model="searchDNI"
          @keypress="handleKeyPress"
          placeholder="INGRESAR DNI"
          inputmode="numeric"
          class="input-xlg"
        />
        <button class="btn btn-primary btn-modern btn-xlg" @click="buscarPorDNI">
          Ingresar
        </button>
      </div>

      <transition name="fade">
        <div v-if="mensajeError" class="inline-error">{{ mensajeError }}</div>
      </transition>
    </section>

    <!-- Acciones tabla -->
    <section class="table-actions">
      <button class="btn btn-success btn-modern" @click="mostrarTablaSocios">Ver Tabla Socios</button>
      <button v-if="tablaSociosMostrar" class="btn btn-outline-danger btn-modern" @click="ocultarTablaSocios">Ocultar Tabla Socios</button>
    </section>

    <!-- Tabla Socios -->
    <div class="table-wrapper">
      <table id="tablaSocios" class="table-modern" style="display:none; margin:0;">
        <thead>
          <tr>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Sede</th>
            <th>Último Pago</th>
            <th>Expiración</th>
            <th>Estado</th>
            <th>Vence en</th>
            <th>Editar</th>
            <th>Eliminar</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="socio in socios" :key="socio.id">
            <td>{{ socio.dni }}</td>
            <td>{{ socio.nombre }}</td>
            <td>{{ socio.apellido }}</td>
            <td>{{ socio.telefono }}</td>
            <td>{{ socio.email }}</td>
            <td>{{ socio.sede }}</td>
            <td>{{ socio.last_pay }}</td>
            <td :class="socio.isExpiring ? 'ok' : 'bad'">{{ socio.expiration }}</td>
            <td>
              <span :class="['badge', socio.isExpiring ? 'badge-success' : 'badge-danger']">
                {{ socio.isExpiring ? 'Activo' : 'Vencido' }}
              </span>
            </td>
            <td :class="socio.isExpiring ? 'ok' : 'bad'">{{ calcularDiasParaVencimiento(socio.expiration) }} días</td>
            <td class="icon-cell">
              <img src="@/assets/edit_button.png" alt="Editar" class="icon-btn" @click="editarSocio(socio)">
            </td>
            <td class="icon-cell">
              <img src="@/assets/delete.svg" alt="Eliminar" class="icon-btn danger" @click="eliminarSocio(socio)">
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal eliminar -->
    <div v-if="mostrarModalDelete" class="custom-modal-container" role="dialog" aria-modal="true">
      <div class="modal-content glass">
        <div class="modal-header">
          <h5 class="modal-title">Confirmar eliminación</h5>
          <button type="button" class="close" @click="cancelarEdicion"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <p>¿Está seguro de que desea eliminar al socio?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-modern" @click="deleteSocio">Eliminar</button>
          <button type="button" class="btn btn-secondary btn-modern" @click="cancelarEdicion">Cancelar</button>
        </div>
      </div>
    </div>

    <!-- Modal crear -->
    <div v-if="mostrarModalCreate" class="custom-modal-container" role="dialog" aria-modal="true">
      <div class="custom-modal-content glass">
        <div class="modal-header">
          <h5 class="modal-title">Crear Socio</h5>
          <button type="button" class="close" @click="cancelarEdicion"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="crearSocio" class="form-columns">
            <div class="form-column">
              <label for="dni">DNI</label>
              <input type="text" id="dni" v-model="socioEditado.dni" required>
              <label for="nombre">Nombre</label>
              <input type="text" id="nombre" v-model="socioEditado.nombre">
              <label for="apellido">Apellido</label>
              <input type="text" id="apellido" v-model="socioEditado.apellido">
              <label for="telefono">Teléfono</label>
              <input type="text" id="telefono" v-model="socioEditado.telefono">
            </div>
            <div class="form-column">
              <label for="email">Email</label>
              <input type="text" id="email" v-model="socioEditado.email">
              <label for="sede">Sede</label>
              <input type="text" id="sede" v-model="socioEditado.sede">
              <label for="price">Precio</label>
              <input type="text" id="price" v-model="socioEditado.price">
              <label for="last_pay">Fecha Último Pago</label>
              <input type="date" id="last_pay" v-model="socioEditado.last_pay" placeholder="Seleccione fecha">
              <label for="expiration">Fecha Vencimiento</label>
              <input type="date" id="expiration" v-model="socioEditado.expiration" placeholder="Seleccione fecha">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-modern" @click="crearSocio">Crear</button>
          <button type="button" class="btn btn-secondary btn-modern" @click="cancelarEdicion">Cancelar</button>
        </div>
      </div>
    </div>

    <!-- Modal editar -->
    <div v-if="mostrarModal" class="custom-modal-container" role="dialog" aria-modal="true">
      <div class="custom-modal-content glass">
        <div class="modal-header">
          <h5 class="modal-title">Editar Socio</h5>
          <button type="button" class="close" @click="cancelarEdicion"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="actualizarSocio" class="form-columns">
            <div class="form-column">
              <label for="dni_edit">DNI</label>
              <input type="text" id="dni_edit" v-model="socioEditado.dni" required>
              <label for="nombre_edit">Nombre</label>
              <input type="text" id="nombre_edit" v-model="socioEditado.nombre">
              <label for="apellido_edit">Apellido</label>
              <input type="text" id="apellido_edit" v-model="socioEditado.apellido">
              <label for="telefono_edit">Teléfono</label>
              <input type="text" id="telefono_edit" v-model="socioEditado.telefono">
            </div>
            <div class="form-column">
              <label for="email_edit">Email</label>
              <input type="text" id="email_edit" v-model="socioEditado.email">
              <label for="sede_edit">Sede</label>
              <input type="text" id="sede_edit" v-model="socioEditado.sede">
              <label for="price_edit">Precio</label>
              <input type="text" id="price_edit" v-model="socioEditado.price">
              <label for="last_pay_edit">Fecha Último Pago</label>
              <input type="date" id="last_pay_edit" v-model="socioEditado.last_pay">
              <label for="expiration_edit">Fecha Vencimiento</label>
              <input type="date" id="expiration_edit" v-model="socioEditado.expiration">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-modern" @click="actualizarSocio">Actualizar</button>
          <button type="button" class="btn btn-secondary btn-modern" @click="cancelarEdicion">Cancelar</button>
        </div>
      </div>
    </div>

    <!-- Modal info socio -->
    <div v-if="mostrarModalSocio" class="custom-modal-container" role="dialog" aria-modal="true">
      <div class="modalsocio-content glass">
        <button class="close close-abs" @click="cerrarModal" aria-label="Cerrar">&times;</button>
        <h2>Información del Socio</h2>
        <div class="modalsocio-body">
          <div class="modal-column">
            <p><strong>DNI:</strong> {{ socioSeleccionado.dni }}</p>
            <p><strong>Nombre:</strong> {{ socioSeleccionado.nombre }}</p>
            <p><strong>Apellido:</strong> {{ socioSeleccionado.apellido }}</p>
            <p><strong>Teléfono:</strong> {{ socioSeleccionado.telefono }}</p>
            <p><strong>Precio:</strong> $ {{ socioSeleccionado.price }}</p>
          </div>
          <div class="modal-column">
            <p><strong>Email:</strong> {{ socioSeleccionado.email }}</p>
            <p><strong>Sede:</strong> {{ socioSeleccionado.sede }}</p>
            <p><strong>Último Pago:</strong> {{ socioSeleccionado.last_pay }}</p>
            <p><strong>Expiración:</strong> {{ socioSeleccionado.expiration }}</p>
            <p><strong>Vence en:</strong> {{ calcularDiasParaVencimiento(socioSeleccionado.expiration) }} días</p>
          </div>
        </div>
        <div :class="['status-bar', socioSeleccionado.isExpiring ? 'ok' : 'bad']">
          <strong>{{ socioSeleccionado.isExpiring ? 'Activo' : 'Vencido' }}</strong>
        </div>
      </div>
    </div>

    <!-- Expiran mañana -->
    <section class="expiracion-container">
      <h2 class="section-title">Socios con vencimiento mañana</h2>
      <ul v-if="sociosExpiracion.length" class="expiracion-list">
        <li v-for="socio in sociosExpiracion" :key="socio.id" class="expiracion-item">
          <div class="socio-info">
            <p><strong>DNI:</strong> {{ socio.dni }}</p>
            <p><strong>Nombre:</strong> {{ socio.nombre }}</p>
            <p><strong>Apellido:</strong> {{ socio.apellido }}</p>
            <p><strong>Teléfono:</strong> {{ socio.telefono }}</p>
            <p><strong>Email:</strong> {{ socio.email }}</p>
            <p><strong>Sede:</strong> {{ socio.sede }}</p>
            <p><strong>Fecha Vencimiento:</strong> {{ socio.expiration }}</p>
          </div>
        </li>
      </ul>
      <p v-else class="no-expiracion-msg">No hay socios con fechas de expiración para mañana.</p>
    </section>
  </div>
</template>



<script src="https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>

<script>
import axios from 'axios';
import $ from 'jquery';
import 'datatables.net';

export default {
  data() {
    return {
      socios: [],
      searchDNI: '',
      mensajeError: '',
      socioSeleccionado: null,
      mostrarModalSocio: false,
      mostrarModal: false,
      mostrarModalCreate: false,
      mostrarModalDelete: false,
      socioEditado: {
        id: null,
        dni: '',
        nombre: '',
        apellido: '',
        telefono: '',
        email: '',
        sede: '',
        last_pay: '',
        expiration: '',
        activo: ''
      },
      exito: false,
      filtroActivo: '',
      sociosFiltrados: [],
      tablaSociosMostrar: false,
      sociosExpiracion: []
    };
  },
  mounted() {
    this.getSocios();
    this.obtenerSociosConExpiracion();
  },
  computed: {
    sociosFiltrados() {
      if (this.searchDNI) {
        return this.socios.filter(socio => socio.dni === this.searchDNI);
      }
      return this.socios;
    },
  },
  methods: {
    obtenerSociosConExpiracion() {
      axios.get(`${process.env.VUE_APP_BACKEND_API_URL}socios/expiracion`) // https://netoboxingcenter.com.ar/api/socios/create http://localhost:8080
        .then(response => {
          this.sociosExpiracion = response.data.sociosExpiracion;
          // if (Array.isArray(this.sociosExpiracion)) {
          //   this.sociosExpiracion.forEach(socio => {
          //     if (socio.email) {
          //       this.enviarAvisoEmail(socio);
          //     }
          //   });
          // } else {
          //   console.error('La respuesta no contiene un array válido de socios');
          // }
        })
        .catch(error => {
          this.mostrarMensajeError();
          this.exito = false;
        });
    },
    calcularDiasParaVencimiento(fechaExpiracion) {
      const expiracion = new Date(fechaExpiracion);
      const hoy = new Date();
      const diferenciaEnTiempo = expiracion.getTime() - hoy.getTime();
      const diferenciaEnDias = Math.ceil(diferenciaEnTiempo / (1000 * 3600 * 24));
      return diferenciaEnDias;
    },
    enviarAvisoEmail(socio) {
      axios.post(`${process.env.VUE_APP_BACKEND_API_URL}socios/enviar-aviso-email`, { // https://netoboxingcenter.com.ar/api/socios/enviar-aviso-email http://localhost:8080
        nombre: socio.nombre,
        email: socio.email
      })
      .then(() => {
        console.log(`Email enviado a ${socio.nombre}`);
      })
      .catch(error => {
        console.error(`Error al enviar email a ${socio.nombre}:`, error);
      });
    },
    getSocios() {
      const user = sessionStorage.getItem('user');
      if (!user) {
        this.$router.push({ path: '/login' });
      }else {
      axios.get(`${process.env.VUE_APP_BACKEND_API_URL}socios/get`)//https://netoboxingcenter.com.ar/api/socios/get http://localhost:8080
        .then(response => {
          this.socios = response.data.socios.map(socio => {
            socio.isExpiring = new Date(socio.expiration) > new Date();
            return socio;
          });
          this.$nextTick(() => {
            $('#tablaSocios').DataTable({
              searching: true,
              language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
              },
            });
          });
        })
        .catch(error => {
          console.error('Error obteniendo socios:', error);
        });
      }
    },
    buscarPorDNI() {
      this.mensajeError = '';
      const socioEncontrado = this.socios.find(socio => socio.dni === this.searchDNI);
      if (!socioEncontrado) {
        this.mensajeError = 'No hay usuarios registrados con ese DNI';
        this.mostrarModalSocio = false;
        setTimeout(() => {
              window.location.reload();
            }, 1000);
      } else {
        this.socioSeleccionado = socioEncontrado;
        this.mostrarModalSocio = true;

        const ingresoData = {
          dni: socioEncontrado.dni,
          nombre: socioEncontrado.nombre,
          apellido: socioEncontrado.apellido,
          sede: socioEncontrado.sede,
          last_pay: socioEncontrado.last_pay,
          expiration: socioEncontrado.expiration,
          fecha_ingreso: new Date().toISOString() // Formato ISO para fecha y hora actual
        };

        setTimeout(() => {
          this.mostrarModalSocio = false;
          this.searchDNI = '';
        }, 4000);

        axios.post(`${process.env.VUE_APP_BACKEND_API_URL}socios/ingresos`, ingresoData) //https://netoboxingcenter.com.ar/api/socios/ingresos http://localhost:8080
        .then(response => {
          console.log('Ingreso registrado:', response.data);
        })
        .catch(error => {
          console.error('Error al registrar ingreso:', error);
        });
      }
    },
    cerrarModal() {
      this.mostrarModalSocio = false;
      console.log(this.mostrarModalSocio);
      this.searchDNI = '';
    },
    handleKeyPress(event) {
      if (event.key === 'Enter') {
        this.buscarPorDNI();
      } else if (event.key === 'Escape') {
        this.cerrarModal();
      }
    },
    // crearSocio() {
    //   axios.post('http://localhost:8080/api/socios/create', this.socioEditado)//https://netoboxingcenter.com.ar/api/socios/create http://localhost:8080
    //     .then(response => {
    //       this.mostrarMensajeExito();
    //       this.exito = true;
    //     })
    //     .catch(error => {
    //       this.mostrarMensajeError();
    //       this.exito = false;
    //     })
    //     .finally(() => {
    //       this.mostrarModalCreate = false;
    //       if (this.exito) {
    //         setTimeout(() => {
    //           window.location.reload();
    //         }, 1500);
    //       }
    //     });
    // },
    crearSocio() {
        axios.get(`${process.env.VUE_APP_BACKEND_API_URL}socios/get`) // https://netoboxingcenter.com.ar/api/socios/get http://localhost:8080
          .then(response => {
            const existeSocio = response.data.socios.some(socio => socio.dni === this.socioEditado.dni);
            console.log('existeSocio', existeSocio);
            if (existeSocio) {
              this.exito = false;
              this.mostrarModalCreate = false;
              this.mostrarMensajeErrorDni();
              return;
            }
          
        axios.post(`${process.env.VUE_APP_BACKEND_API_URL}socios/create`, this.socioEditado) // https://netoboxingcenter.com.ar/api/socios/create http://localhost:8080
          .then(response => {
            this.mostrarMensajeExito();
            this.exito = true;
          })
          .catch(error => {
            this.mostrarMensajeError();
            this.exito = false;
          })
          .finally(() => {
            this.mostrarModalCreate = false;
            if (this.exito) {
              setTimeout(() => {
                window.location.reload();
              }, 1500);
            }
          });
        })
        .catch(error => {
          this.mostrarMensajeError('Error al verificar el DNI');
          console.error('Error obteniendo socios:', error);
          this.exito = false;
        });
    },
    editarSocio(socio) {
        this.mostrarModal = true;
        this.socioEditado = { ...socio };
      },
      eliminarSocio(socio) {
        this.mostrarModalDelete = true;
        this.socioEditado = { ...socio };
      },
      actualizarSocio() {
      axios.put(`${process.env.VUE_APP_BACKEND_API_URL}socios/edit/${this.socioEditado.id}`, this.socioEditado)//https://netoboxingcenter.com.ar/api/socios/edit/${this.socioEditado.id}
        .then(response => {
          this.mostrarMensajeUpdate();
          this.exito = true;
        })
        .catch(error => {
          this.mostrarMensajeError();
          this.exito = false;
        })
        .finally(() => {
          this.mostrarModal = false;
          if (this.exito) {
            setTimeout(() => {
              window.location.reload();
            }, 1500);
          }
        });      
    },
    deleteSocio(id) {
      axios.delete(`${process.env.VUE_APP_BACKEND_API_URL}socios/delete/${this.socioEditado.id}`)//https://netoboxingcenter.com.ar/api/socios/delete/${this.socioEditado.id}
      .then(response => {
        this.mostrarMensajeDelete();
          this.exito = true;
        })
        .catch(error => {
          this.mostrarMensajeError();
          this.exito = false;
        })
        .finally(() => {
          this.mostrarModalDelete = false;
          if (this.exito) {
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          }
        });      
    },
    cancelarEdicion() {
      this.mostrarModal = false;
      this.mostrarModalCreate = false;
      this.mostrarModalDelete = false;
    },
    goToIngresos(){
      this.$router.push("/ingresos");
    },
    goToAcceso(){
      this.$router.push("/acceso");
    },
    logout(){
      sessionStorage.removeItem('user');
      this.$router.push({ path: '/' });
    },
    mostrarMensajeError() {
      document.getElementById('mensajeError').style.display = 'block';
      setTimeout(() => {
        document.getElementById('mensajeError').style.display = 'none';
      }, 5000); // Ocultar el mensaje después de 5 segundos
    },
    mostrarMensajeExito() {
      document.getElementById('mensajeExito').style.display = 'block';
      setTimeout(() => {
        document.getElementById('mensajeExito').style.display = 'none';
      }, 5000); // Ocultar el mensaje después de 5 segundos
    },
    mostrarMensajeUpdate() {
      document.getElementById('mensajeUpdate').style.display = 'block';
      setTimeout(() => {
        document.getElementById('mensajeUpdate').style.display = 'none';
      }, 5000); // Ocultar el mensaje después de 5 segundos
    },
    mostrarMensajeDelete() {
      document.getElementById('mensajeDelete').style.display = 'block';
      setTimeout(() => {
        document.getElementById('mensajeDelete').style.display = 'none';
      }, 5000); // Ocultar el mensaje después de 5 segundos
    },
    mostrarMensajeErrorDni() {
      document.getElementById('mensajeErrorDNI').style.display = 'block';
      setTimeout(() => {
        document.getElementById('mensajeErrorDNI').style.display = 'none';
      }, 5000); // Ocultar el mensaje después de 5 segundos
    },
    mostrarMensajeToast(id) {
      const el = document.getElementById(id);
      if(el) {
        el.style.display = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 5000);
      }
    },
    mostrarTablaSocios() {
      const tabla = document.getElementById('tablaSocios');
      if (tabla) {
        tabla.style.display = '';
        tabla.style.width = '100%';
      }
      this.tablaSociosMostrar = true;
    },
    ocultarTablaSocios(){
      const tabla = document.getElementById('tablaSocios');
      if (tabla) {
        tabla.style.display = 'none';
      }
    }
  }
};
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap');
@import '~bootstrap/dist/css/bootstrap.min.css';

/* ── Variables ── */
:root {
  --bg:          #040507;
  --surface:     #0f1115;
  --surface-2:   #151922;
  --surface-3:   #1b2030;
  --text:        #e6e9ef;
  --muted:       #a8b3cf;
  --border:      #232a3a;
  --accent:      #7c5cff;
  --yellow:      #CED800;
  --yellow-soft: #f9ff79;
  --success:     #22c55e;
  --danger:      #ef4444;
  --radius-lg:   16px;
  --radius-md:   12px;
  --radius-sm:   8px;
}

* { box-sizing: border-box; }

body, html, #app {
  background: var(--bg) !important;
  color: var(--text) !important;
  font-family: 'Outfit', system-ui, sans-serif !important;
  margin: 0; padding: 0;
}

/* ── App shell ── */
.app-shell {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  gap: 0;
  background: var(--bg);
}

/* ── Header ── */
.app-header {
  position: sticky;
  top: 0;
  z-index: 20;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 28px;
  background: rgba(15, 17, 21, 0.92);
  backdrop-filter: blur(12px) saturate(1.2);
  border-bottom: 1px solid var(--border);
  box-shadow: 0 2px 20px rgba(0,0,0,0.4);
}
.brand { display: flex; align-items: center; gap: 12px; }
.brand-dot {
  width: 10px; height: 10px; border-radius: 99px;
  background: var(--yellow);
  box-shadow: 0 0 10px var(--yellow);
}
.app-header h1 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  letter-spacing: .3px;
  color: var(--text);
}
.header-actions { display: flex; gap: 10px; align-items: center; }

/* ── Buttons ── */
.btn-modern {
  border-radius: var(--radius-md) !important;
  border: 1px solid var(--border) !important;
  background: var(--surface-3) !important;
  color: var(--text) !important;
  padding: 9px 16px !important;
  font-family: 'Outfit', sans-serif !important;
  font-size: 14px !important;
  font-weight: 600 !important;
  cursor: pointer !important;
  transition: all .2s ease !important;
}
.btn-modern:hover {
  background: var(--surface-2) !important;
  border-color: rgba(255,255,255,.12) !important;
  transform: translateY(-1px) !important;
  box-shadow: 0 4px 16px rgba(0,0,0,.35) !important;
}
.btn-modern.btn-success {
  background: rgba(34, 197, 94, .12) !important;
  border-color: rgba(34, 197, 94, .3) !important;
  color: #86efac !important;
}
.btn-modern.btn-success:hover {
  background: rgba(34, 197, 94, .2) !important;
  box-shadow: 0 4px 18px rgba(34, 197, 94, .15) !important;
}
.btn-modern.btn-primary {
  background: rgba(206, 216, 0, .12) !important;
  border-color: rgba(206, 216, 0, .3) !important;
  color: var(--yellow) !important;
}
.btn-modern.btn-primary:hover {
  background: rgba(206, 216, 0, .2) !important;
  box-shadow: 0 4px 18px rgba(206, 216, 0, .12) !important;
}
.btn-modern.btn-danger {
  background: rgba(239, 68, 68, .12) !important;
  border-color: rgba(239, 68, 68, .3) !important;
  color: #fca5a5 !important;
}
.btn-modern.btn-danger:hover {
  background: rgba(239, 68, 68, .22) !important;
  box-shadow: 0 4px 18px rgba(239, 68, 68, .15) !important;
}
.btn-modern.btn-outline-danger {
  background: transparent !important;
  border-color: rgba(239, 68, 68, .35) !important;
  color: #fca5a5 !important;
}
.btn-modern.btn-secondary {
  background: var(--surface-2) !important;
  border-color: var(--border) !important;
  color: var(--muted) !important;
}
.btn-modern.btn-xlg {
  padding: 16px 22px !important;
  font-size: 17px !important;
  border-radius: 14px !important;
}

/* ── Toasts ── */
.toast-stack {
  position: fixed;
  top: 74px; right: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 40;
}
.toast-item {
  padding: 12px 16px;
  border-radius: var(--radius-md);
  border: 1px solid var(--border);
  background: var(--surface-2);
  color: var(--text);
  font-size: 14px;
  font-weight: 500;
  box-shadow: 0 8px 24px rgba(0,0,0,.4);
  min-width: 260px;
}
.toast-success { border-left: 4px solid var(--success); }
.toast-error   { border-left: 4px solid var(--danger); }
.toast-info    { border-left: 4px solid var(--accent); }

/* ── Access card (DNI Kiosk) ── */
.access-card {
  width: min(1100px, 94vw);
  margin: 28px auto 0;
  padding: 48px 32px;
  border: 1px solid var(--border);
  border-radius: 22px;
  background:
    radial-gradient(900px 500px at 100% -40%, rgba(206,216,0,.07), transparent),
    radial-gradient(700px 400px at 0% 110%, rgba(124,92,255,.07), transparent),
    linear-gradient(180deg, var(--surface-2), rgba(15,17,21,.9));
  box-shadow: 0 12px 40px rgba(0,0,0,.5), 0 0 0 1px rgba(206,216,0,.06) inset;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.access-card::before {
  content: "";
  position: absolute; inset: 0;
  background: radial-gradient(50% 40% at 50% 0%, rgba(206,216,0,.08), transparent 70%);
  pointer-events: none;
}
.section-title {
  margin: 0 0 8px;
  font-size: 26px;
  font-weight: 800;
  color: var(--text);
  letter-spacing: .2px;
}
.section-subtitle {
  margin: 0 0 28px;
  color: var(--muted);
  font-size: 15px;
}
.section-subtitle kbd {
  background: var(--surface-3);
  border: 1px solid var(--border);
  border-radius: 5px;
  padding: 2px 7px;
  font-size: 12px;
  color: var(--text);
}
.access-inputs {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 14px;
  align-items: center;
}
.input-xlg {
  width: 100%;
  height: 68px;
  border-radius: 16px;
  border: 1px solid var(--border);
  background: rgba(4,5,7,.8);
  color: var(--text);
  font-size: 24px;
  font-family: 'Outfit', sans-serif;
  font-weight: 600;
  letter-spacing: 1px;
  text-align: center;
  outline: none;
  transition: all .2s ease;
}
.input-xlg::placeholder { color: #4a5568; }
.input-xlg:focus {
  border-color: var(--yellow);
  box-shadow: 0 0 0 4px rgba(206,216,0,.15), 0 8px 30px rgba(206,216,0,.06);
  transform: translateY(-1px);
}
.inline-error {
  margin-top: 14px;
  color: #fca5a5;
  font-weight: 600;
  font-size: 15px;
}

/* ── Table actions ── */
.table-actions {
  width: min(1200px, 94vw);
  margin: 20px auto 0;
  display: flex;
  gap: 12px;
}
.table-wrapper {
  width: min(1200px, 94vw);
  margin: 10px auto 28px;
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  background: var(--surface);
  overflow: hidden;
}

/* ── Table modern ── */
.table-modern {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}
.table-modern thead th {
  position: sticky; top: 0;
  background: #0a0c10;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .5px;
  font-weight: 700;
  font-size: 11px;
  padding: 14px 12px;
  border-bottom: 1px solid var(--border);
}
.table-modern tbody td {
  padding: 12px;
  border-top: 1px solid rgba(35,42,58,.6);
  color: var(--text);
  font-size: 13px;
}
.table-modern tbody tr:hover { background: rgba(206,216,0,.03); }

/* ── Badges ── */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 99px;
  font-weight: 700;
  font-size: 11px;
  letter-spacing: .3px;
  border: 1px solid transparent;
}
.badge-success {
  background: rgba(34,197,94,.12);
  color: #86efac;
  border-color: rgba(34,197,94,.22);
}
.badge-danger {
  background: rgba(239,68,68,.12);
  color: #fca5a5;
  border-color: rgba(239,68,68,.22);
}

/* ── Expiration row states ── */
.ok { color: #86efac !important; font-weight: 600; }
.bad { color: #fca5a5 !important; font-weight: 600; }

/* ── Icon buttons ── */
.icon-cell { text-align: center; }
.icon-btn {
  width: 24px; height: 24px; cursor: pointer;
  opacity: .7; transition: all .15s ease;
  filter: grayscale(20%);
}
.icon-btn:hover { opacity: 1; transform: scale(1.12); filter: grayscale(0%); }
.icon-btn.danger:hover { filter: drop-shadow(0 0 8px rgba(239,68,68,.5)); }

/* ── DataTables dark overrides ── */
.dataTables_wrapper {
  padding: 16px !important;
  color: var(--text) !important;
  font-family: 'Outfit', sans-serif !important;
}
.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_info {
  color: var(--muted) !important;
  font-size: 13px !important;
}
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
  background: rgba(4,5,7,.8) !important;
  border: 1px solid var(--border) !important;
  border-radius: var(--radius-sm) !important;
  color: var(--text) !important;
  padding: 6px 10px !important;
  outline: none !important;
  font-family: 'Outfit', sans-serif !important;
}
.dataTables_wrapper .dataTables_filter input:focus,
.dataTables_wrapper .dataTables_length select:focus {
  border-color: var(--yellow) !important;
  box-shadow: 0 0 0 3px rgba(206,216,0,.12) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
  background: var(--surface-3) !important;
  border: 1px solid var(--border) !important;
  color: var(--muted) !important;
  border-radius: var(--radius-sm) !important;
  margin: 2px !important;
  padding: 5px 10px !important;
  font-size: 13px !important;
  cursor: pointer !important;
  transition: all .15s ease !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: var(--surface-2) !important;
  color: var(--text) !important;
  border-color: rgba(255,255,255,.1) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
  background: rgba(206,216,0,.15) !important;
  border-color: rgba(206,216,0,.35) !important;
  color: var(--yellow) !important;
  font-weight: 700 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
  opacity: .35 !important;
  cursor: not-allowed !important;
}
.sorting::after, .sorting_asc::after, .sorting_desc::after {
  color: var(--muted) !important;
}

/* ── Modals ── */
.custom-modal-container {
  position: fixed; inset: 0;
  display: flex; align-items: center; justify-content: center;
  padding: 20px;
  z-index: 50;
  background: rgba(4,6,10,.7);
  backdrop-filter: blur(8px);
}
.custom-modal-content,
.modal-content,
.modalsocio-content {
  width: min(860px, 95vw);
  border-radius: 22px;
  border: 1px solid var(--border);
  background: linear-gradient(160deg, rgba(21,25,34,.97) 0%, rgba(11,14,20,.97) 100%);
  color: var(--text);
  box-shadow: 0 24px 64px rgba(0,0,0,.6), 0 0 0 1px rgba(206,216,0,.05) inset;
  overflow: hidden;
}
.glass { backdrop-filter: blur(12px) saturate(1.1); }

.modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 18px 22px;
  border-bottom: 1px solid var(--border);
  background: rgba(255,255,255,.02);
}
.modal-title {
  margin: 0;
  font-weight: 700;
  font-size: 16px;
  color: var(--text);
}
.close {
  background: transparent; border: none;
  color: var(--muted);
  font-size: 26px; line-height: 1;
  cursor: pointer;
  transition: color .15s ease;
  padding: 0; margin: 0;
}
.close:hover { color: var(--text); }
.close-abs { position: absolute; right: 18px; top: 14px; }

.modal-body { padding: 22px; }
.modal-footer {
  display: flex; justify-content: flex-end; gap: 10px;
  padding: 14px 22px;
  border-top: 1px solid var(--border);
  background: rgba(255,255,255,.01);
}

/* ── Form columns (in modals) ── */
.form-columns {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px 24px;
}
.form-column {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.form-column label {
  color: var(--muted);
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .6px;
}
.form-column input {
  width: 100%;
  height: 46px;
  border: 1px solid var(--border);
  background: rgba(4,5,7,.9);
  color: var(--text);
  border-radius: var(--radius-md);
  padding: 0 14px;
  font-family: 'Outfit', sans-serif;
  font-size: 14px;
  outline: none;
  transition: all .2s ease;
}
.form-column input:focus {
  border-color: var(--yellow);
  box-shadow: 0 0 0 3px rgba(206,216,0,.13);
}

/* ── Socio info modal ── */
.modalsocio-content {
  padding: 22px;
  position: relative;
}
.modalsocio-content h2 {
  font-size: 20px;
  font-weight: 800;
  margin-bottom: 18px;
  color: var(--text);
}
.modalsocio-body {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px 24px;
  font-size: 15px;
  margin-bottom: 18px;
}
.modalsocio-body p { margin: 0; color: var(--muted); }
.modalsocio-body p strong { color: var(--text); }
.status-bar {
  text-align: center;
  padding: 12px;
  border-radius: var(--radius-md);
  border: 1px solid var(--border);
  font-weight: 700;
  margin-top: 6px;
}
.status-bar.ok  { background: rgba(34,197,94,.1);  border-color: rgba(34,197,94,.25);  color: #86efac; }
.status-bar.bad { background: rgba(239,68,68,.1);  border-color: rgba(239,68,68,.25);  color: #fca5a5; }

/* ── Expiración section ── */
.expiracion-container {
  width: min(1100px, 94vw);
  margin: 0 auto 32px;
  padding: 24px 20px;
  border: 1px solid var(--border);
  border-radius: 20px;
  background: var(--surface);
}
.expiracion-container .section-title { font-size: 20px; }
.expiracion-list {
  list-style: none; margin: 16px 0 0; padding: 0;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 12px;
}
.expiracion-item {
  padding: 14px 16px;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  background: linear-gradient(160deg, var(--surface-2), var(--surface));
  transition: border-color .2s ease;
}
.expiracion-item:hover { border-color: rgba(206,216,0,.2); }
.socio-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4px 10px;
  font-size: 13px;
}
.socio-info p { margin: 0; color: var(--muted); }
.socio-info p strong { color: var(--text); }
.no-expiracion-msg {
  margin-top: 12px;
  color: var(--muted);
  font-weight: 500;
  font-size: 15px;
  text-align: center;
}

/* ── Transitions ── */
.fade-enter-active, .fade-leave-active { transition: opacity .18s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* ── Responsive ── */
@media (max-width: 768px) {
  .app-header { padding: 12px 16px; flex-wrap: wrap; gap: 10px; }
  .access-inputs { grid-template-columns: 1fr; }
  .form-columns { grid-template-columns: 1fr; }
  .modalsocio-body { grid-template-columns: 1fr; }
  .socio-info { grid-template-columns: 1fr; }
  .table-actions { flex-wrap: wrap; }
}
</style>