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
      axios.get('https://netoboxingcenter.com.ar/api/socios/expiracion') // https://netoboxingcenter.com.ar/api/socios/create http://localhost:8080
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
      axios.post('https://netoboxingcenter.com.ar/api/socios/enviar-aviso-email', { // https://netoboxingcenter.com.ar/api/socios/enviar-aviso-email http://localhost:8080
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
      axios.get('https://netoboxingcenter.com.ar/api/socios/get')//https://netoboxingcenter.com.ar/api/socios/get http://localhost:8080
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

        axios.post('https://netoboxingcenter.com.ar/api/socios/ingresos', ingresoData) //https://netoboxingcenter.com.ar/api/socios/ingresos http://localhost:8080
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
        axios.get('https://netoboxingcenter.com.ar/api/socios/get') // https://netoboxingcenter.com.ar/api/socios/get http://localhost:8080
          .then(response => {
            const existeSocio = response.data.socios.some(socio => socio.dni === this.socioEditado.dni);
            console.log('existeSocio', existeSocio);
            if (existeSocio) {
              this.exito = false;
              this.mostrarModalCreate = false;
              this.mostrarMensajeErrorDni();
              return;
            }
          
        axios.post('https://netoboxingcenter.com.ar/api/socios/create', this.socioEditado) // https://netoboxingcenter.com.ar/api/socios/create http://localhost:8080
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
      axios.put(`https://netoboxingcenter.com.ar/api/socios/edit/${this.socioEditado.id}`, this.socioEditado)//https://netoboxingcenter.com.ar/api/socios/edit/${this.socioEditado.id}
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
      axios.delete(`https://netoboxingcenter.com.ar/api/socios/delete/${this.socioEditado.id}`)//https://netoboxingcenter.com.ar/api/socios/delete/${this.socioEditado.id}
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
@import '~bootstrap/dist/css/bootstrap.min.css';

:root{
  --bg: #0f1115;
  --surface: #151922;
  --surface-2: #1b2030;
  --text: #e6e9ef;
  --muted: #a8b3cf;
  --border: #2a3040;
  --accent: #7c5cff;          /* acento violeta */
  --secondary: #CED800;       /* AMARILLO principal (tu color de marca) */
  --secondary-soft: #F9FF79;  /* AMARILLO suave para brillos */
  --success: #22c55e;
  --danger: #ef4444;
  --warning: #f59e0b;
}

*{ box-sizing: border-box; }

body, #app { background: var(--bg); color: var(--text); }

.app-shell{
  min-height: 100vh;
  display: grid;
  grid-template-rows: auto 1fr;
  gap: 24px;
}

/* Header */
.app-header{
  position: sticky;
  top: 0;
  z-index: 20;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 24px;
  background: linear-gradient(180deg, rgba(21,25,34,0.85), rgba(21,25,34,0.55));
  backdrop-filter: saturate(1.1) blur(8px);
  border-bottom: 1px solid var(--border);
}
.brand{ display:flex; align-items:center; gap:10px; }
.brand-dot{
  width:10px; height:10px; border-radius:999px; background: var(--accent);
  box-shadow: 0 0 12px var(--accent);
}
.app-header h1{
  margin:0; font-size: 20px; font-weight: 700; letter-spacing: .2px;
}
.header-actions{ display:flex; gap:10px; }

/* Buttons */
.btn-modern{
  border-radius: 10px !important;
  border: 1px solid var(--border) !important;
  background: var(--surface-2) !important;
  color: var(--text) !important;
  padding: 10px 14px !important;
  transition: transform .08s ease, background .2s ease, box-shadow .2s ease;
}
.btn-modern.btn-xlg{
  padding: 16px 20px !important;
  font-size: 18px !important;
  border-radius: 12px !important;
}
.btn-modern.btn-primary:hover{
  box-shadow: 0 8px 24px rgba(124,92,255,.18), 0 0 0 3px rgba(206,216,0,.12) inset;
}

/* Variante secundaria (si querés usarla en algún botón) */
.btn-modern--secondary{
  background: linear-gradient(180deg, rgba(206,216,0,.22), rgba(206,216,0,.12)) !important;
  border-color: rgba(206,216,0,.35) !important;
  color: #111 !important;
}
.btn-modern--secondary:hover{
  box-shadow: 0 8px 22px rgba(206,216,0,.25);
  filter: saturate(1.1);
}

/* Toasts */
.toast-stack{
  position: fixed;
  top: 84px;
  right: 16px;
  display: grid;
  gap: 10px;
  z-index: 40;
}
.toast-item{
  padding: 10px 14px;
  border-radius: 10px;
  border: 1px solid var(--border);
  background: var(--surface-2);
  color: var(--text);
  box-shadow: 0 8px 20px rgba(0,0,0,.25);
  min-width: 260px;
}
.toast-success{ border-left: 4px solid var(--success); }
.toast-error{ border-left: 4px solid var(--danger); }
.toast-info{ border-left: 4px solid var(--accent); }

/* Acceso Socio card */
.access-card{
  width: min(1100px, 94vw);               /* antes 920px */
  margin: 16px auto 0;                    /* un poco más de aire */
  padding: 40px 28px;                     /* antes ~28px */
  border: 1px solid var(--border);
  border-radius: 18px;                    /* un toque más redondeado */
  background:
    radial-gradient(1200px 600px at 100% -60%, rgba(124,92,255,.10), transparent),
    radial-gradient(900px 400px at 0% 120%, rgba(206,216,0,.10), transparent), /* amarillo suave */
    linear-gradient(180deg, var(--surface), rgba(27,32,48,.85));
  box-shadow:
    0 10px 30px rgba(0,0,0,.35),
    0 0 0 1px rgba(206,216,0,.08) inset;  /* filo leve amarillo */
  text-align: center;
  position: relative;
  overflow: hidden;
}

/* halo sutil amarillo detrás */
.access-card::after{
  content:"";
  position:absolute; inset: -30%;
  background: radial-gradient(40% 30% at 50% 10%, rgba(249,255,121,.10), transparent 60%);
  pointer-events:none;
  filter: blur(18px);
}
.section-title{
  margin: 0 0 6px;
  font-size: 24px;             /* un poquito más grande */
  font-weight: 800;
  letter-spacing: .2px;
  text-shadow: 0 1px 0 rgba(0,0,0,.35);
}
.section-subtitle{
  margin: 0 0 20px;
  color: var(--muted);
  font-size: 15px;
}
.access-inputs{
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 12px;
  align-items: center;
  justify-content: center;
}
.input-xlg{
  width: 100%;
  height: 64px;                              /* antes 56px */
  border-radius: 14px;                       /* un poco más */
  border: 1px solid var(--border);
  background: #0b0e14;
  color: var(--text);
  font-size: 22px;                           /* más grande para kiosk */
  letter-spacing: .8px;
  text-align: center;
  outline: none;
  transition: box-shadow .2s ease, border .2s ease, transform .06s ease;
}
.input-xlg::placeholder{ color: #9aa3b5; }
.input-xlg:focus{
  border-color: var(--secondary);
  box-shadow: 0 0 0 5px rgba(206,216,0,.18), 0 8px 28px rgba(206,216,0,.08);
  transform: translateY(-1px);
}


/* Inline error (bajo el input) */
.inline-error{
  margin-top: 12px;
  color: var(--danger);
  font-weight: 600;
}

/* Tabla */
.table-actions{
  width: min(1200px, 94vw);
  margin: 12px auto 0;
  display: flex;
  gap: 10px;
}
.table-wrapper{
  width: min(1200px, 94vw);
  margin: 8px auto 24px;
  border: 1px solid var(--border);
  border-radius: 14px;
  background: var(--surface);
  overflow: hidden;
}
.table-modern{
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}
.table-modern thead th{
  position: sticky;
  top: 0;
  background: #111521;
  color: #cfd6e6;
  text-transform: uppercase;
  letter-spacing: .4px;
  font-weight: 700;
  padding: 12px;
  border-bottom: 1px solid var(--border);
}
.table-modern tbody td{
  padding: 12px;
  border-top: 1px solid var(--border);
  color: var(--text);
}
.table-modern tbody tr:nth-child(odd){ background: rgba(255,255,255,.02); }
.table-modern tbody tr:hover{ background: rgba(124,92,255,.08); }

.icon-cell{ text-align: center; }
.icon-btn{
  width: 22px; height: 22px; cursor: pointer; opacity: .85; transition: transform .08s ease, opacity .2s ease;
}
.icon-btn:hover{ transform: scale(1.06); opacity: 1; }
.icon-btn.danger:hover{ filter: drop-shadow(0 0 8px rgba(239,68,68,.45)); }

/* Badges + estado */
.badge{
  display: inline-block;
  padding: 6px 10px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 12px;
  letter-spacing: .2px;
  border: 1px solid transparent;
}
.badge-success{ background: rgba(34,197,94,.15); color: #86efac; border-color: rgba(34,197,94,.25); }
.badge-danger{ background: rgba(239,68,68,.15); color: #fca5a5; border-color: rgba(239,68,68,.25); }

.ok{ background-color: green; color: white; }
.bad{ background-color: red; color: white; }

/* Modales (glass) */
.custom-modal-container{
  position: fixed; inset: 0; display: grid; place-items: center;
  padding: 18px; z-index: 50;
  background: rgba(4,6,10,.55);
  backdrop-filter: blur(6px);
}
.custom-modal-content,
.modal-content,
.modalsocio-content{
  width: min(880px, 94vw);
  border-radius: 16px;
  border: 1px solid var(--border);
  background: linear-gradient(180deg, rgba(21,25,34,.95), rgba(21,25,34,.92));
  color: var(--text);
  box-shadow: 0 20px 60px rgba(0,0,0,.5);
}
.glass{ backdrop-filter: blur(10px) saturate(1.1); }

.modal-header{
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 16px; border-bottom: 1px solid var(--border);
}
.modal-title{ margin:0; font-weight: 700; }
.close{
  background: transparent; border: none; color: var(--muted); font-size: 28px;
}
.close:hover{ color: var(--text); cursor: pointer; }
.close-abs{ position: absolute; right: 16px; top: 10px; }

.modal-body{ padding: 16px; }
.modal-footer{
  display:flex; justify-content:flex-end; gap:10px;
  padding: 12px 16px; border-top: 1px solid var(--border);
}

/* Formularios 2 columnas */
.form-columns{
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px 20px;
}
.form-column label{
  display:block; margin: 6px 0 6px; color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: .4px;
}
.form-column input{
  width: 100%; border: 1px solid var(--border); background: #0b0e14; color: var(--text);
  border-radius: 10px; padding: 10px 12px; outline: none;
}
.form-column input:focus{ border-color: var(--accent); box-shadow: 0 0 0 3px rgba(124,92,255,.15); }

/* Modal socio */
.modalsocio-content{ padding: 18px 16px 16px; position: relative; }
.modalsocio-body{
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px 20px; font-size: 16px;
}
.status-bar{
  margin-top: 10px; text-align: center; padding: 10px; border-radius: 12px;
  border: 1px solid var(--border);
}

/* Expiración mañana */
.expiracion-container{
  width: min(920px, 92vw);
  margin: 0 auto 24px; padding: 18px 16px;
  border: 1px solid var(--border);
  border-radius: 16px;
  background: var(--surface);
}
.expiracion-list{ list-style: none; margin: 0; padding: 0; display: grid; gap: 12px; }
.expiracion-item{
  padding: 12px; border: 1px solid var(--border); border-radius: 12px;
  background: linear-gradient(180deg, #121726, #0f1420);
}
.socio-info{ display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 6px 12px; }
.no-expiracion-msg{ text-align:center; color: var(--muted); font-weight: 600; }

/* Animaciones y helpers */
.fade-enter-active, .fade-leave-active { transition: opacity .18s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Responsive */
@media (max-width: 720px){
  .access-inputs{ grid-template-columns: 1fr; }
  .form-columns{ grid-template-columns: 1fr; }
  .modalsocio-body{ grid-template-columns: 1fr; }
}

</style>