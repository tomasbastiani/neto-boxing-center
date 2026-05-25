<template>
  <div id="app" class="app-container">
    <NavbarComponent/>
    <main class="main-content">
      <router-view></router-view>
    </main>
    <FooterComponent/>
  </div>
</template>

<script>
import NavbarComponent from './components/NavBar.vue';
import FooterComponent from './components/Footer.vue';

export default {
  name: 'App',
  components: {
    NavbarComponent,
    FooterComponent
  }
}
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body, html {
  height: 100%;
  background-color: #040507;
}

.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

#app {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #e6e9ef;
  margin-top: 0 !important;
}
</style>
