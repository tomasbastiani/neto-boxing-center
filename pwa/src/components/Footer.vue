<template>
    <footer class="text-light py-3">
      <div class="container">
        <div class="row">
          <!-- Logo a la izquierda -->
          <div class="col-md-6">
            <img
              src="https://i.postimg.cc/4dfPpWvJ/logoneto.png"
              alt="Logo"
              height="100"
              class="d-inline-block align-top"
            />
          </div>
          <!-- Datos de contacto a la derecha -->
          <div class="col-md-6 text-right">
            <div>
              <strong>Dirección:</strong> Av. Diaz Velez 5204 - Av. Gaona 1880
            </div>
            <div>
              <strong>Teléfono:</strong> 11 5052-4048
            </div>
            <div>
              <strong>Horario:</strong> Lunes a Sábados
            </div>
          </div>
        </div>
        <hr class="my-4">
        <!-- Texto de copyright -->
        <div class="text-center">
          <p>&copy; 2024 Neto Boxing Center. Todos los derechos reservados.</p>
        </div>
      </div>
    </footer>
  </template>
  
  <script>
  export default {
    name: "FooterComponent",
  };
  </script>
  
  <style scoped>
    footer{
        background-color: rgba(4,5,7,255);
    }
  </style>
  