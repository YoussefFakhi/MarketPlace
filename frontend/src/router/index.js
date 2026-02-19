// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/Login.vue'),
    meta: { guestOnly: true } // meta it like a tag 
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/Register.vue'),
    meta: { guestOnly: true } // meta it like a tag 
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/Dashboard.vue'),
    meta: { requiresAuth: true } // meta it like a tag 
  },
  {
    path: '/services',
    name: 'services',
    component: () => import('../views/Services.vue'),
    meta: { requiresAuth: true } // meta it like a tag 
  },
  {
    path: '/my-services',
    name: 'my-services',
    component: () => import('../views/MyServices.vue'),
    meta: { requiresAuth: true, role: 'freelancer' } // meta it like a tag 
  },
  {
    path: '/orders',
    name: 'orders',
    component: () => import('../views/Orders.vue'),
    meta: { requiresAuth: true } // meta it like a tag 
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// 1. to - Where user wants to go
// 2. from - Where user came from
// 3. next - The green light to proceed

router.beforeEach(async (to, from, next) => {   // This code runs BEFORE the user can visit any page
  const authStore = useAuthStore();

  // If user is trying to access guest-only routes but is logged in
  if (to.meta.guestOnly && authStore.isAuthenticated) {
    return next({ name: 'dashboard' });
  }

  // If route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      return next({ name: 'login' });
    }

    // If route requires specific role
    if (to.meta.role && authStore.user?.role !== to.meta.role) {
      alert("You don't have permission to access this page.");
      return next({ name: 'dashboard' });
    }
  }

  next();
});

export default router;