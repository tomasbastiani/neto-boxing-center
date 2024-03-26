import Vue from 'vue';
import App from './App.vue';
import router from './router'; // Importa tu archivo de enrutador

Vue.config.productionTip = false;

new Vue({
  router, // Usa el enrutador en la instancia de Vue
  render: h => h(App),
}).$mount('#app');
