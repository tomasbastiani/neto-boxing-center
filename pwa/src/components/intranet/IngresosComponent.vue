<template>
  <div class="ingresos-shell">
    <!-- Header -->
    <header class="ingresos-header">
      <button class="btn-back" @click="$router.push('/admin')" aria-label="Volver al panel">
        <span class="back-arrow">←</span>
        <span>Volver</span>
      </button>

      <div class="header-title">
        <span class="header-dot"></span>
        <h1>Registro de Ingresos</h1>
      </div>

      <div class="header-spacer"></div>
    </header>

    <!-- Table wrapper -->
    <div class="table-outer">
      <table id="ingresosTable" class="display ingresos-table">
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
          <tr v-for="ingreso in ingresos" :key="ingreso.id">
            <td>{{ ingreso.dni }}</td>
            <td>{{ ingreso.nombre }}</td>
            <td>{{ ingreso.apellido }}</td>

            <td>
              <span
                :class="[
                  'estado-badge',
                  ingreso.estado === 'Activo'
                    ? 'badge-success'
                    : 'badge-danger'
                ]"
              >
                {{ ingreso.estado }}
              </span>
            </td>

            <td>{{ ingreso.sede }}</td>
            <td>{{ ingreso.fecha_ingreso }}</td>
            <td>{{ ingreso.expiration }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import "datatables.net";
import moment from "moment";

export default {
  data() {
    return {
      ingresos: [],
    };
  },

  methods: {
    async fetchIngresos() {
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_BACKEND_API_URL}socios/get_ingresos`
        );

        let ingresosRaw = response.data;

        // Filtrar duplicados por dni, nombre, apellido y fecha_ingreso
        const seen = new Set();

        const ingresosUnicos = ingresosRaw.filter((ingreso) => {
          const fechaIngreso = moment(ingreso.fecha_ingreso).format(
            "YYYY-MM-DD"
          );

          const key = `${ingreso.dni}-${ingreso.nombre}-${ingreso.apellido}-${fechaIngreso}`;

          if (seen.has(key)) {
            return false;
          }

          seen.add(key);
          return true;
        });

        // Calcular estado
        this.ingresos = ingresosUnicos.map((ingreso) => {
          const fechaIngreso = moment(ingreso.fecha_ingreso);
          const expiration = moment(ingreso.expiration);

          ingreso.estado = fechaIngreso.isAfter(expiration)
            ? "Vencido"
            : "Activo";

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
    },
  },

  mounted() {
    this.fetchIngresos();
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap');

/* ── Shell ── */
.ingresos-shell {
  min-height: 100vh;
  background: #040507;
  color: #e6e9ef;
  font-family: 'Outfit', system-ui, sans-serif;
  display: flex;
  flex-direction: column;
  gap: 0;
}

/* ── Header ── */
.ingresos-header {
  position: sticky;
  top: 0;
  z-index: 20;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 28px;
  background: rgba(15, 17, 21, 0.92);
  backdrop-filter: blur(12px) saturate(1.2);
  border-bottom: 1px solid #232a3a;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.4);
}

.btn-back {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(206, 216, 0, 0.08);
  border: 1px solid rgba(206, 216, 0, 0.2);
  border-radius: 12px;
  color: #CED800;
  font-family: 'Outfit', sans-serif;
  font-size: 14px;
  font-weight: 600;
  padding: 8px 16px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-back:hover {
  background: rgba(206, 216, 0, 0.16);
  border-color: rgba(206, 216, 0, 0.35);
  transform: translateX(-2px);
}

.back-arrow {
  font-size: 18px;
  line-height: 1;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-dot {
  width: 10px;
  height: 10px;
  border-radius: 99px;
  background: #CED800;
  box-shadow: 0 0 10px #CED800;
}

.header-title h1 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: #e6e9ef;
}

.header-spacer {
  width: 100px;
}

/* ── Table outer ── */
.table-outer {
  padding: 24px 28px;
  flex: 1;
}

.ingresos-table {
  width: 100% !important;
  border-collapse: collapse;
  font-size: 13px;
}

/* ── Estado badge ── */
.estado-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 99px;
  font-weight: 700;
  font-size: 11px;
  letter-spacing: 0.3px;
  border: 1px solid transparent;
}

.badge-success {
  background: rgba(34, 197, 94, 0.12);
  color: #86efac;
  border-color: rgba(34, 197, 94, 0.22);
}

.badge-danger {
  background: rgba(239, 68, 68, 0.12);
  color: #fca5a5;
  border-color: rgba(239, 68, 68, 0.22);
}
</style>

<style>
/* ── DataTables dark overrides ── */

#ingresosTable_wrapper {
  color: #e6e9ef !important;
  font-family: 'Outfit', sans-serif !important;
}

#ingresosTable_wrapper .dataTables_filter label,
#ingresosTable_wrapper .dataTables_length label,
#ingresosTable_wrapper .dataTables_info {
  color: #a8b3cf !important;
  font-size: 13px !important;
  font-family: 'Outfit', sans-serif !important;
}

#ingresosTable_wrapper .dataTables_filter input,
#ingresosTable_wrapper .dataTables_length select {
  background: rgba(4, 5, 7, 0.9) !important;
  border: 1px solid #232a3a !important;
  border-radius: 8px !important;
  color: #e6e9ef !important;
  padding: 6px 10px !important;
  outline: none !important;
  font-family: 'Outfit', sans-serif !important;
}

#ingresosTable_wrapper .dataTables_filter input:focus,
#ingresosTable_wrapper .dataTables_length select:focus {
  border-color: #CED800 !important;
  box-shadow: 0 0 0 3px rgba(206, 216, 0, 0.12) !important;
}

#ingresosTable thead th {
  background: #0a0c10 !important;
  color: #a8b3cf !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  font-size: 11px !important;
  font-weight: 700 !important;
  padding: 14px 12px !important;
  border-bottom: 1px solid #232a3a !important;
  font-family: 'Outfit', sans-serif !important;
}

#ingresosTable tbody td {
  padding: 12px !important;
  border-top: 1px solid rgba(35, 42, 58, 0.5) !important;
  color: #e6e9ef !important;
  background: #0f1115 !important;
  font-family: 'Outfit', sans-serif !important;
}

#ingresosTable tbody tr:nth-child(even) td {
  background: rgba(255, 255, 255, 0.015) !important;
}

#ingresosTable tbody tr:hover td {
  background: rgba(206, 216, 0, 0.03) !important;
}

#ingresosTable_wrapper .dataTables_paginate .paginate_button {
  background: #1b2030 !important;
  border: 1px solid #232a3a !important;
  color: #a8b3cf !important;
  border-radius: 8px !important;
  margin: 2px !important;
  padding: 5px 10px !important;
  font-size: 13px !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
}

#ingresosTable_wrapper .dataTables_paginate .paginate_button:hover {
  background: #151922 !important;
  color: #e6e9ef !important;
  border-color: rgba(255, 255, 255, 0.1) !important;
}

#ingresosTable_wrapper .dataTables_paginate .paginate_button.current,
#ingresosTable_wrapper .dataTables_paginate .paginate_button.current:hover {
  background: rgba(206, 216, 0, 0.15) !important;
  border-color: rgba(206, 216, 0, 0.35) !important;
  color: #CED800 !important;
  font-weight: 700 !important;
}

#ingresosTable_wrapper .dataTables_paginate .paginate_button.disabled {
  opacity: 0.35 !important;
  cursor: not-allowed !important;
}
</style>