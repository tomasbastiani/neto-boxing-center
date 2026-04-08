// import Vue from 'vue';
// import Router from 'vue-router';
// import HomeComponent from '@/components/HomeComponent.vue';
// import LoginComponent from '@/components/intranet/LoginComponent.vue';
// import AdminComponent from '@/components/intranet/AdminComponent.vue';
// import IngresosComponent from '@/components/intranet/IngresosComponent.vue';

// Vue.use(Router);

// export default new Router({
//   routes: [
//     {
//       path: '/',
//       name: 'HomeComponent',
//       component: HomeComponent
//     },
//     {
//       path: '/login', // Corregido: ruta absoluta
//       name: 'LoginComponent',
//       component: LoginComponent
//     },
//     {
//       path: '/admin', // Corregido: ruta absoluta
//       name: 'AdminComponent',
//       component: AdminComponent
//     },
//     {
//       path: '/ingresos',
//       name: 'IngresosComponent',
//       component: IngresosComponent,
//     },
//   ],
// });


// src/router/index.js
import Vue from 'vue';
import Router from 'vue-router';
import HomeComponent from '@/components/HomeComponent.vue';
import LoginComponent from '@/components/intranet/LoginComponent.vue';
import AdminComponent from '@/components/intranet/AdminComponent.vue';
import IngresosComponent from '@/components/intranet/IngresosComponent.vue';

Vue.use(Router);

const router = new Router({
  mode: 'history', // opcional
  routes: [
    { path: '/', name: 'HomeComponent', component: HomeComponent },
    { path: '/login', name: 'LoginComponent', component: LoginComponent },
    { path: '/admin', name: 'AdminComponent', component: AdminComponent, meta: { requiresAuth: true } },
    {
      path: '/ingresos',
      name: 'IngresosComponent',
      component: IngresosComponent,
      meta: { requiresAuth: true } // ← ruta protegida
    },
  ],
});

// Guard global
router.beforeEach((to, from, next) => {
  const isAuth = !!sessionStorage.getItem('user'); // tu flag de login

  // si la ruta requiere auth y no está logueado → login con redirect
  if (to.matched.some(r => r.meta.requiresAuth) && !isAuth) {
    return next({ path: '/login', query: { redirect: to.fullPath } });
  }

  // si ya está logueado y va a /login → redirigir a /ingresos (o a donde venía)
  if (to.path === '/login' && isAuth) {
    return next(to.query.redirect || '/ingresos');
  }

  next();
});

export default router;
