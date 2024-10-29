<template>
  <div id="container">
    <div style="margin-bottom: 10px; padding: 20px;">
      <h1>Administracion de Socios</h1>
      <button class="btn btn-success" @click="mostrarModalCreate = true"> + Nuevo Socio</button>
    </div>
    <div id="mensajeError" style="display: none;" class="error-message">Ocurrio un error.</div>
    <div id="mensajeErrorDNI" style="display: none;" class="error-message">El DNI ya se encuentra registrado.</div>
    <div id="mensajeDelete" style="display: none;" class="success-message">Socio eliminado con exito!</div>
    <div id="mensajeExito" style="display: none;" class="success-message">Socio creado con exito!</div>
    <div id="mensajeUpdate" style="display: none;" class="success-message">Socio actualizado con exito!</div>
    <br>
    <div id="busquedaSocio">
      <h2>Acceso Socio</h2>
      <br>
      <input v-focus v-model="searchDNI" @keypress="handleKeyPress" placeholder="INGRESAR DNI" />
      <br>
      <button @click="buscarPorDNI">Ingresar</button>
    </div>

    <br>
    <div v-if="mensajeError" class="error-message">{{ mensajeError }}</div>
    <br>
    <!-- display: none; -->
    <button style="margin-left: 10px;" class="btn btn-success" @click="mostrarTablaSocios">Ver Tabla Socios</button>
    <br>
    <br>
    <button v-if="tablaSociosMostrar" style="margin-left: 10px;" class="btn btn-danger" @click="ocultarTablaSocios">Ocultar Tabla Socios</button>
    <br>
    <br>
    <table id="tablaSocios" style="margin-bottom: 10px; margin-top: 10px; display: none;">
      <thead>
        <tr>
          <th>DNI</th>
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Telefono</th>
          <th>Email</th>
          <th>Sede</th>
          <!-- <th>Precio</th> -->
          <th>Ultimo Pago</th>
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
          <!-- <td>$ {{ socio.price }}</td> -->
          <td>{{ socio.last_pay }}</td>
          <td :class="{ 'expiring': !socio.isExpiring, 'notExpiring': socio.isExpiring  }">{{ socio.expiration }}</td>
          <td :class="{ 'expiring': !socio.isExpiring, 'notExpiring': socio.isExpiring  }">{{ socio.isExpiring ? 'Activo' : 'Vencido' }}</td>
          <td :class="{ 'expiring': !socio.isExpiring, 'notExpiring': socio.isExpiring  }">{{ calcularDiasParaVencimiento(socio.expiration) }} Dias</td>
          <td>
            <img src="@/assets/edit_button.png" alt="Editar" @click="editarSocio(socio)">
          </td>
          <td>
            <img src="@/assets/delete.svg" alt="Eliminar" @click="eliminarSocio(socio)">
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="mostrarModalDelete" class="custom-modal-container" role="document">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-white">
            <h5 class="modal-title">Confirmar eliminación</h5>
            <button type="button" class="close" @click="cancelarEdicion">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body bg-white">
            <p>¿Está seguro de que desea eliminar al socio?</p>
          </div>
          <div class="modal-footer bg-white border-top-0">
            <button type="button" class="btn btn-danger" @click="deleteSocio">Eliminar</button>
            <button type="button" class="btn btn-secondary" @click="cancelarEdicion">Cancelar</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="mostrarModalCreate" class="custom-modal-container" role="document">
        <div class="custom-modal-content">
          <!-- Encabezado del modal -->
          <div class="modal-header">
            <h5 class="modal-title">Crear Socio</h5>
            <button type="button" class="close" @click="cancelarEdicion">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <!-- Contenido del modal (formulario de creacion) -->
          <div class="modal-body">
            <form @submit.prevent="crearSocio" class="form-columns">
              <!-- Columna izquierda -->
              <div class="form-column">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" v-model="socioEditado.dni" required>

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" v-model="socioEditado.nombre">

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" v-model="socioEditado.apellido">

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" v-model="socioEditado.telefono">

                <!-- <label for="telefono">Activo:</label>
                <input type="text" id="telefono" v-model="socioEditado.activo"> -->
              </div>

              <!-- Columna derecha -->
              <div class="form-column">
                <label for="email">Email:</label>
                <input type="text" id="email" v-model="socioEditado.email">

                <label for="sede">Sede:</label>
                <input type="text" id="sede" v-model="socioEditado.sede">

                <label for="sede">Precio:</label>
                <input type="text" id="price" v-model="socioEditado.price">

                <label for="last_pay">Fecha Último Pago:</label>
                <input type="date" id="expiration" v-model="socioEditado.last_pay" placeholder="Seleccione fecha">

                <label for="expiration">Fecha Vencimiento:</label>
                <input type="date" id="expiration" v-model="socioEditado.expiration" placeholder="Seleccione fecha">
              </div>
            </form>
          </div>
          <!-- Footer del modal (botones de acción) -->
          <div class="modal-footer">
            <button type="submit" class="btn btn-success" @click="crearSocio">Crear</button>
            <button type="button" class="btn btn-secondary" @click="cancelarEdicion">Cancelar</button>
          </div>
        </div>
      </div>

      <div v-if="mostrarModal" class="custom-modal-container" role="document">
        <div class="custom-modal-content">
          <!-- Encabezado del modal -->
          <div class="modal-header">
            <h5 class="modal-title">Editar Socio</h5>
            <button type="button" class="close" @click="cancelarEdicion">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <!-- Contenido del modal (formulario de edición) -->
          <div class="modal-body">
            <form @submit.prevent="actualizarSocio" class="form-columns">
              <!-- Columna izquierda -->
              <div class="form-column">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" v-model="socioEditado.dni" required>

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" v-model="socioEditado.nombre">

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" v-model="socioEditado.apellido">

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" v-model="socioEditado.telefono">
                
                <!-- <label for="telefono">¿Activo?:</label>
                <input type="text" id="telefono" v-model="socioEditado.activo"> -->
              </div>

              <!-- Columna derecha -->
              <div class="form-column">
                <label for="email">Email:</label>
                <input type="text" id="email" v-model="socioEditado.email">

                <label for="sede">Sede:</label>
                <input type="text" id="sede" v-model="socioEditado.sede">

                <label for="sede">Precio:</label>
                <input type="text" id="price" v-model="socioEditado.price">

                <label for="last_pay">Fecha Último Pago:</label>
                <input type="date" id="expiration" v-model="socioEditado.last_pay" placeholder="Seleccione fecha">

                <label for="expiration">Fecha Vencimiento:</label>
                <input type="date" id="expiration" v-model="socioEditado.expiration" placeholder="Seleccione fecha">
              </div>
            </form>
          </div>
          <!-- Footer del modal (botones de acción) -->
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" @click="actualizarSocio">Actualizar</button>
            <button type="button" class="btn btn-secondary" @click="cancelarEdicion">Cancelar</button>
          </div>
        </div>
      </div>
      <br>
      <!-- Modal para mostrar la información del socio -->
      <div v-if="mostrarModalSocio" class="modalSocio">
        <div class="modalsocio-content">
          <span class="close" @click="cerrarModal">&times;</span>
          <h2>Información del Socio</h2>
          <div class="modalsocio-body">
            <div class="modal-column">
              <p><strong>DNI:</strong> {{ socioSeleccionado.dni }}</p>
              <p><strong>Nombre:</strong> {{ socioSeleccionado.nombre }}</p>
              <p><strong>Apellido:</strong> {{ socioSeleccionado.apellido }}</p>
              <p><strong>Telefono:</strong> {{ socioSeleccionado.telefono }}</p>
              <p><strong>Precio:</strong> $ {{ socioSeleccionado.price }}</p>
            </div>
            <div class="modal-column">
              <p><strong>Email:</strong> {{ socioSeleccionado.email }}</p>
              <p><strong>Sede:</strong> {{ socioSeleccionado.sede }}</p>
              <p><strong>Ultimo Pago:</strong> {{ socioSeleccionado.last_pay }}</p>
              <p>
                <strong>Expiración:</strong> {{ socioSeleccionado.expiration }}
              </p>
              <p>
                <strong>Vence en:</strong> {{ calcularDiasParaVencimiento(socioSeleccionado.expiration) }} Dias
              </p>
            </div>
          </div>
          <br>
          <div :class="['status-bar', socioSeleccionado.isExpiring ? 'notExpiring' : 'expiring']">
            <strong>{{ socioSeleccionado.isExpiring ? 'Activo' : 'Vencido' }}</strong>
          </div>
        </div>
      </div>
    <b-button @click="logout" class="btn btn-danger btn-sm" style="margin-left: 10px;">Cerrar Sesión</b-button>
    <div class="expiracion-container">
        <h1>Socios con vencimiento Mañana</h1>
        <ul v-if="sociosExpiracion.length" class="expiracion-list">
          <li v-for="socio in sociosExpiracion" :key="socio.id" class="expiracion-item">
              <div class="socio-info">
                  <p><strong>DNI:</strong> {{ socio.dni }}</p>
                  <p><strong>Nombre:</strong> {{ socio.nombre }}</p>
                  <p><strong>Apellido:</strong> {{ socio.apellido }}</p>
                  <p><strong>Telefono:</strong> {{ socio.telefono }}</p>
                  <p><strong>Email:</strong> {{ socio.email }}</p>
                  <p><strong>Sede:</strong> {{ socio.sede }}</p>
                  <p><strong>Fecha Vencimiento:</strong> {{ socio.expiration }}</p>
              </div>
          </li>
        </ul>
        <p v-else class="no-expiracion-msg">No hay socios con fechas de expiración para mañana.</p>
    </div>
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
      axios.get('http://localhost:8080/api/socios/expiracion') // https://netoboxingcenter.com.ar/api/socios/create http://localhost:8080
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
        setTimeout(() => {
          this.mostrarModalSocio = false;
          this.searchDNI = '';
      }, 4000); // Ocultar el mensaje después de 5 segundos
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
/* Estilos CSS para la tabla, opcional */
table {
  width: 100%;
  border-collapse: collapse;
}

#tablaSocios{
  width: 100%;
}

th, td {
  border: 1px solid #ccc;
  padding: 10px;
}

th {
  background-color: #f2f2f2;
}

.custom-modal-container {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Fondo semitransparente */
    z-index: 1000; /* Coloca el modal por encima del resto del contenido */
}

.custom-modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
    max-width: 80%; /* Ancho máximo del modal */
}

.form-columns {
  display: flex;
  justify-content: space-between;
}

.form-column {
  flex: 1;
  margin-right: 20px; /* Espacio entre las columnas */
}

/* Estilos opcionales para ajustar el ancho de los labels */
.form-column label {
  display: block;
  width: 100%;
  margin-bottom: 5px;
}

.modal-footer{
  padding: 10px;
}

#container{
  margin-top: 20px;
  margin-bottom: 20px;
}

.error-message {
  background-color: #ffcccc;
  color: #ff0000;
  padding: 10px;
  border: 1px solid #ff0000;
  border-radius: 5px;
}

.success-message {
  background-color: #ccffd7;
  color: green;
  padding: 10px;
  border: 1px solid #00ff22;
  border-radius: 5px;
}

.expiring {
  background-color: #ff0000;
}

.notExpiring{
  background-color: #00ff22;
}

.modalSocio {
  display: block;
  position: fixed;
  z-index: 1; 
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto; 
  background-color: rgb(0,0,0); 
  background-color: rgba(0,0,0,0.4); 
}
.modalsocio-content {
  background-color: #E2E2E2;
  margin: 10% auto; 
  padding: 20px;
  border: 1px solid #888;
  width: 70%;
  height: 65%;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}
.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
.modalsocio-body {
  font-size: large;
  display: flex;
  justify-content: space-between;
}
.modal-column {
  width: 50%;
}
.status-bar {
  text-align: center;
  font-size: 30px;
  padding: 10px;
  width: 100%;
  margin-right: auto;
  margin-left: auto;
}
.expiring.status-bar {
  background-color: red;
  color: black;
  margin-right: auto;
  margin-left: auto;
}
.notExpiring.status-bar {
  background-color: green;
  color: black;
  margin-right: auto;
  margin-left: auto;
}
#busquedaSocio {
  border: solid 2px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 60%;
  height: 50vh;
  background-color: #CED800;
  margin-right: auto;
  margin-left: auto;
}

#busquedaSocio input {
  width: 200px; /* Tamaño deseado */
  height: 40px; /* Altura deseada */
  font-size: 16px; /* Tamaño de fuente deseado */
  margin-bottom: 10px; /* Espaciado entre el input y el botón */
  color: white;
  background-color: black;
}

#busquedaSocio button {
  width: 100px; /* Tamaño deseado */
  height: 40px; /* Altura deseada */
  font-size: 16px; /* Tamaño de fuente deseado */
  color: white;
  background-color: black;
}
#busquedaSocio input::placeholder {
  text-align: center;
  color: white; /* Color del texto del placeholder */
}
#busquedaSocio img {
  align-self: flex-start; /* Ubica la imagen a la izquierda */
  margin-left: 10px; /* Opcional: Espaciado desde el borde izquierdo */
}

.expiracion-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 20px;
}

.expiracion-list {
    list-style: none;
    padding: 0;
}

.expiracion-item {
    background-color: #F9FF79;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 15px;
    padding: 15px;
    transition: transform 0.2s ease;
}

.expiracion-item:hover {
    transform: translateY(-3px);
}

.socio-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.socio-info p {
    margin: 5px 0;
    font-size: 1rem;
    color: #555;
}

.socio-info strong {
    color: #000;
}

.no-expiracion-msg {
    text-align: center;
    color: #ff4c4c;
    font-size: 1.2rem;
    font-weight: bold;
    margin-top: 20px;
}

/* Responsivo */
@media (max-width: 600px) {
    .expiracion-container {
        padding: 10px;
    }

    h1 {
        font-size: 1.5rem;
    }

    .expiracion-item {
        padding: 10px;
    }

    .socio-info p {
        font-size: 0.9rem;
    }
}



</style>