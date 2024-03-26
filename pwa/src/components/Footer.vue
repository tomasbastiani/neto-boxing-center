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
              <strong>Dirección:</strong> Tu Dirección
            </div>
            <div>
              <strong>Teléfono:</strong> Tu Teléfono
            </div>
            <div>
              <strong>Email:</strong> tu@email.com
            </div>
            <div>
              <strong>Horario:</strong> Lunes a Viernes, 9:00 AM - 5:00 PM
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
  