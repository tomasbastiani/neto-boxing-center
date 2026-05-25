<template>
  <div class="home-container">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <div class="brand-badge">GIMNASIO DE BOXEO</div>
        <h1 class="hero-title">NETO BOXING CENTER</h1>
        <p class="hero-subtitle">Disciplina • Fuerza • Respeto</p>
        <div class="hero-actions">
          <a href="https://api.whatsapp.com/send?phone=1150524048" target="_blank" class="btn-custom btn-primary-custom">
            Entrená con Nosotros
          </a>
        </div>
      </div>
    </section>

    <!-- Sedes & Features Section -->
    <section class="features-section container">
      <div class="row g-4 justify-content-center">
        <!-- Card 1: Sedes -->
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">📍</div>
            <h3>Nuestras Sedes</h3>
            <p>Entrená en cualquiera de nuestras sucursales:</p>
            <ul class="sede-list">
              <li>Av. Díaz Vélez 5204</li>
              <li>Av. Gaona 1880</li>
            </ul>
          </div>
        </div>
        
        <!-- Card 2: Horarios -->
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">📅</div>
            <h3>Horarios</h3>
            <p>Abierto de lunes a sábados.</p>
            <p class="highlight-text">Clases personalizadas y grupales adaptadas a todos los niveles, desde principiantes hasta competición.</p>
          </div>
        </div>

        <!-- Card 3: Filosofía -->
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">🥊</div>
            <h3>Filosofía Neto</h3>
            <p>El boxeo no es solo golpear; es estrategia, superación constante y salud mental.</p>
            <span class="motto">"Tu único rival sos vos mismo."</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'HomeComponent',
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;600;800&display=swap');
@import '~bootstrap/dist/css/bootstrap.min.css';

.home-container {
  background-color: #040507;
  color: #e6e9ef;
  font-family: 'Outfit', sans-serif;
  min-height: calc(100vh - 180px);
  padding-bottom: 60px;
}

/* Hero Section */
.hero-section {
  position: relative;
  height: 70vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-image: url(../assets/img/fondo5.jpg);
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  text-align: center;
  overflow: hidden;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(4, 5, 7, 0.4) 0%, rgba(4, 5, 7, 0.95) 100%);
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
  max-width: 800px;
  padding: 0 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  animation: fadeInUp 1s ease-out;
}

.brand-badge {
  background: rgba(206, 216, 0, 0.15);
  color: #CED800;
  border: 1px solid rgba(206, 216, 0, 0.3);
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 2px;
  margin-bottom: 20px;
  text-transform: uppercase;
}

.hero-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(3.5rem, 8vw, 6.5rem);
  color: #fff;
  letter-spacing: 2px;
  line-height: 0.95;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
  margin-bottom: 10px;
  background: linear-gradient(180deg, #ffffff 40%, #CED800 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-subtitle {
  font-size: clamp(1.2rem, 3vw, 1.8rem);
  color: #a8b3cf;
  font-weight: 300;
  letter-spacing: 4px;
  text-transform: uppercase;
  margin-bottom: 35px;
}

.hero-actions {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  justify-content: center;
}

/* Custom Buttons */
.btn-custom {
  padding: 14px 28px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-primary-custom {
  background: #CED800;
  color: #040507;
  border: 1px solid #CED800;
}

.btn-primary-custom:hover {
  background: #f9ff79;
  border-color: #f9ff79;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(206, 216, 0, 0.35);
  color: #040507;
}

.btn-outline-custom {
  background: transparent;
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(4px);
}

.btn-outline-custom:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: #fff;
  transform: translateY(-2px);
  color: #fff;
}

/* Features Section */
.features-section {
  margin-top: -40px;
  position: relative;
  z-index: 5;
  padding: 0 15px;
}

.feature-card {
  background: rgba(21, 25, 34, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(12px);
  border-radius: 20px;
  padding: 35px 25px;
  height: 100%;
  transition: all 0.3s ease;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.feature-card:hover {
  transform: translateY(-6px);
  border-color: rgba(206, 216, 0, 0.3);
  box-shadow: 0 20px 40px rgba(206, 216, 0, 0.05);
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 20px;
}

.feature-card h3 {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 15px;
  color: #fff;
}

.feature-card p {
  color: #a8b3cf;
  font-size: 15px;
  line-height: 1.6;
  margin-bottom: 15px;
}

.sede-list {
  list-style: none;
  padding: 0;
  margin: 0;
  width: 100%;
}

.sede-list li {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.03);
  padding: 8px 12px;
  border-radius: 8px;
  margin-bottom: 8px;
  font-weight: 600;
  color: #fff;
  font-size: 14px;
}

.highlight-text {
  font-weight: 600;
  color: #CED800 !important;
}

.motto {
  font-style: italic;
  color: #CED800;
  font-weight: 600;
  margin-top: auto;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  padding-top: 15px;
  width: 100%;
  display: block;
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .hero-section {
    height: 60vh;
  }
  .features-section {
    margin-top: 0;
    padding-top: 30px;
  }
  .hero-actions {
    flex-direction: column;
    width: 100%;
    max-width: 300px;
  }
  .btn-custom {
    width: 100%;
    justify-content: center;
  }
}
</style>
  