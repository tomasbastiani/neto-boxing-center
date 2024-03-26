import Vue from 'vue';
import Router from 'vue-router';
import HomeComponent from '@/components/HomeComponent.vue';
import LoginComponent from '@/components/intranet/LoginComponent.vue';
import AdminComponent from '@/components/intranet/AdminComponent.vue';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'HomeComponent',
      component: HomeComponent
    },
    {
      path: '/login', // Corregido: ruta absoluta
      name: 'LoginComponent',
      component: LoginComponent
    },
    {
      path: '/admin', // Corregido: ruta absoluta
      name: 'AdminComponent',
      component: AdminComponent
    },
  ],
});
