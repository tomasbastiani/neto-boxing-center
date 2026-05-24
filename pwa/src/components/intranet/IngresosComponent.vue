<template>
    <div>
      <div class="title-container">
        <span @click="$router.push('/admin')" class="back-arrow">←</span>
        <h2>Registro de Ingresos</h2>
      </div>
  
      <table id="ingresosTable" class="display">
        <thead>
          <tr>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Estado</th>
            <th>Sede</th>
            <th>Fecha de Ingreso</th>
            <th>Expiración</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ingreso in ingresos" :key="ingreso.id" 
              :class="{'activo': ingreso.estado === 'Activo', 'inactivo': ingreso.estado === 'Vencido'}">
            <td>{{ ingreso.dni }}</td>
            <td>{{ ingreso.nombre }}</td>
            <td>{{ ingreso.apellido }}</td>
            <td>{{ ingreso.estado }}</td>
            <td>{{ ingreso.sede }}</td>
            <td>{{ ingreso.fecha_ingreso }}</td>
            <td>{{ ingreso.expiration }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  import $ from 'jquery';
  import 'datatables.net';
  import moment from 'moment'; // Usamos moment.js para manejar las fechas más fácilmente
  
  export default {
    data() {
      return {
        ingresos: [],
      };
    },
    methods: {
      // async fetchIngresos() {
      //   try {
      //     const response = await axios.get("http://localhost:8080/api/socios/get_ingresos");//https://netoboxingcenter.com.ar/api/login http://localhost:8080
      //     this.ingresos = response.data;
  
      //     // Calculamos el estado de cada ingreso
      //     this.ingresos = this.ingresos.map(ingreso => {
      //       // Convertimos las fechas a objetos moment
      //       const fechaIngreso = moment(ingreso.fecha_ingreso);
      //       const expiration = moment(ingreso.expiration);
  
      //       // Verificamos si la fecha de ingreso es mayor que la fecha de expiración
      //       if (fechaIngreso.isAfter(expiration)) {
      //         ingreso.estado = 'Vencido';
      //       } else {
      //         ingreso.estado = 'Activo';
      //       }
  
      //       return ingreso;
      //     });
  
      //     this.$nextTick(() => {
      //       $("#ingresosTable").DataTable({
      //         language: {
      //           url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
      //         },
      //         order: [[5, "desc"]], // Ordena por fecha_ingreso DESC
      //       });
      //     });
      //   } catch (error) {
      //     console.error("Error al obtener ingresos:", error);
      //   }
      // },

      async fetchIngresos() {
        try {
          const response = await axios.get(`${process.env.VUE_APP_BACKEND_API_URL}socios/get_ingresos`);
          let ingresosRaw = response.data;

          // Filtrar duplicados por dni, nombre, apellido y fecha_ingreso (solo día)
          const seen = new Set();
          const ingresosUnicos = ingresosRaw.filter(ingreso => {
            const fechaIngreso = moment(ingreso.fecha_ingreso).format('YYYY-MM-DD'); // Solo día
            const key = `${ingreso.dni}-${ingreso.nombre}-${ingreso.apellido}-${fechaIngreso}`;
            if (seen.has(key)) {
              return false;
            } else {
              seen.add(key);
              return true;
            }
          });

          // Calcular estado
          this.ingresos = ingresosUnicos.map(ingreso => {
            const fechaIngreso = moment(ingreso.fecha_ingreso);
            const expiration = moment(ingreso.expiration);

            ingreso.estado = fechaIngreso.isAfter(expiration) ? 'Vencido' : 'Activo';
            return ingreso;
          });

          this.$nextTick(() => {
            $("#ingresosTable").DataTable({
              language: {
                url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
              },
              order: [[5, "desc"]],
            });
          });
        } catch (error) {
          console.error("Error al obtener ingresos:", error);
        }
      }

    },
    mounted() {
      this.fetchIngresos();
    },
  };
  </script>
  
  <style scoped>
  .display {
    width: 100%;
    margin-top: 20px;
  }
  
  /* Color verde para estado activo */
  .activo {
    background-color: green;
    color: white;
  }
  
  /* Color rojo para estado vencido */
  .inactivo {
    background-color: red;
    color: white;
  }

  .title-container {
  display: flex;
  align-items: center;
  gap: 10px;
}

.back-arrow {
  cursor: pointer;
  font-size: 40px;
  color: grey;
  transition: color 0.3s;
  padding: 25px;
}

.back-arrow:hover {
  color: black;
}
  </style>
  