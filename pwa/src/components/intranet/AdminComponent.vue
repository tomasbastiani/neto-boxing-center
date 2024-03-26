<template>
  <div id="container">
    <div style="margin-bottom: 10px; padding: 20px;">
      <h1>Administracion de Socios</h1>
      <button class="btn btn-success" @click="mostrarModalCreate = true"> + Nuevo Socio</button>
    </div>
    <table id="tablaSocios" style="margin-bottom: 10px; margin-top: 10px;">
      <thead>
        <tr>
          <th>DNI</th>
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Telefono</th>
          <th>Email</th>
          <th>Sede</th>
          <th>Precio</th>
          <th>Ultimo Pago</th>
          <th>Expiracion</th>
          <th>Activo</th>
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
          <td>{{ socio.price }}</td>
          <td>{{ socio.last_pay }}</td>
          <td>{{ socio.expiration }}</td>
          <td>{{ socio.activo }}</td>
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
                <input type="text" id="last_pay" v-model="socioEditado.last_pay">

                <label for="expiration">Fecha Vencimiento:</label>
                <input type="text" id="expiration" v-model="socioEditado.expiration">
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
                <input type="text" id="last_pay" v-model="socioEditado.last_pay">

                <label for="expiration">Fecha Vencimiento:</label>
                <input type="text" id="expiration" v-model="socioEditado.expiration">
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
    <b-button @click="logout" class="btn btn-danger btn-sm" style="margin-left: 10px;">Cerrar Sesión</b-button>
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
      socios: [], // Array para almacenar los datos de los socios obtenidos de la API
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
      },
    };
  },
  mounted() {
    // Llamar a un método para obtener los socios al cargar el componente
    this.getSocios();
  },
  methods: {
    getSocios() {
      // Llamar a la API para obtener los socios
      // Aquí puedes usar Axios u otra librería para hacer la solicitud HTTP
      // En este ejemplo, asumimos que se obtiene la lista de socios desde una API
      // Puedes reemplazar esta lógica con la llamada a tu propia API
      // Ejemplo ficticio:
      axios.get('http://localhost:8080/api/socios/get')
        .then(response => {
          this.socios = response.data.socios;
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
    },
    crearSocio() {
      // Enviar una solicitud POST a la ruta correspondiente en tu API para crear un nuevo socio
      axios.post('http://localhost:8080/api/socios/create', this.socioEditado)
        .then(response => {
          this.getSocios();
        })
        .catch(error => {
          console.error('Error al crear socio:', error);
          // Manejar el error según sea necesario
        })
        .finally(() => {
          this.mostrarModalCreate = false; // Cerrar el modal después de crear el socio
          window.location.reload();
        });
    },
    // Método para abrir el modal de edición
    editarSocio(socio) {
        this.mostrarModal = true;
        // Copiar datos del socio seleccionado al socioEditado
        this.socioEditado = { ...socio };
      },
      eliminarSocio(socio) {
        this.mostrarModalDelete = true;
        // Copiar datos del socio seleccionado al socioEditado
        this.socioEditado = { ...socio };
      },
      // Método para cancelar la edición y cerrar el modal
      actualizarSocio() {
      axios.put(`http://localhost:8080/api/socios/edit/${this.socioEditado.id}`, this.socioEditado)
        .then(response => {
          console.log('Socio actualizado:', response.data.socio);
        })
        .catch(error => {
          console.error('Error al actualizar socio:', error);
          // Manejar el error según sea necesario
        })
        .finally(() => {
          this.mostrarModal = false; // Cerrar el modal después de actualizar
          window.location.reload();
        });      
    },
    deleteSocio(id) {
      axios.delete(`http://localhost:8080/api/socios/delete/${this.socioEditado.id}`)
      .then(response => {
          // Actualizar la lista de socios después de eliminar
          this.getSocios();
        })
        .catch(error => {
          // Manejar errores en la solicitud de eliminación
          console.error('Error al eliminar socio:', error);
        })
        .finally(() => {
          this.mostrarModalDelete = false; // Cerrar el modal después de actualizar
          window.location.reload();
        });      
    },
    cancelarEdicion() {
      this.mostrarModal = false;
      this.mostrarModalCreate = false;
      this.mostrarModalDelete = false;
    },
    logout(){
      localStorage.clear();
      this.$router.push({ path: '/' });
    }
  },
};
</script>

<style>
@import '~bootstrap/dist/css/bootstrap.min.css';
/* Estilos CSS para la tabla, opcional */
table {
  width: 100%;
  border-collapse: collapse;
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

</style>
