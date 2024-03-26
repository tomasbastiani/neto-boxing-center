<template>
  <div>
    <NavbarComponent/>
    <router-view></router-view>
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
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
