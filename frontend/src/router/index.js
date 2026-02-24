import { createRouter, createWebHashHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    redirect: '/dashboard',
  },

  // SHARED ROUTES (all authenticated users)
  {
    meta: { title: 'Login', guestOnly: true },
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
  },
  {
    meta: { title: 'Profile', requiresAuth: true },
    path: '/profile',
    name: 'profile',
    component: () => import('@/views/ProfileView.vue'),
  },
  {
    meta: { title: 'Error' },
    path: '/error',
    name: 'error',
    component: () => import('@/views/ErrorView.vue'),
  },

  //ADMIN ROUTES
  {
    meta: { title: 'Dashboard', requiresAuth: true, role: 'admin' },
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/HomeView.vue'),
  },
  {
    meta: { title: 'Services', requiresAuth: true, role: 'admin' },
    path: '/services-moderation',
    name: 'services-moderation',
    component: () => import('@/views/ServicesView.vue'),
  },
  {
    meta: { title: 'Categories', requiresAuth: true, role: 'admin' },
    path: '/categories',
    name: 'categories',
    component: () => import('@/views/CategoriesView.vue'),
  },
  {
    meta: { title: 'Orders', requiresAuth: true, role: 'admin' },
    path: '/orders-monitoring',
    name: 'orders-monitoring',
    component: () => import('@/views/OrdersView.vue'),
  },
  {
    meta: { title: 'Users', requiresAuth: true, role: 'admin' },
    path: '/users-management',
    name: 'users-management',
    component: () => import('@/views/UsersView.vue'),
  },

  // FREELANCER ROUTES
  {
    meta: { title: 'My Dashboard', requiresAuth: true, role: 'freelancer' },
    path: '/freelancer/dashboard',
    name: 'freelancer-dashboard',
    component: () => import('@/views/freelancer/DashboardView.vue'),
  },
  {
    meta: { title: 'My Services', requiresAuth: true, role: 'freelancer' },
    path: '/freelancer/services',
    name: 'freelancer-services',
    component: () => import('@/views/freelancer/MyServicesView.vue'),
  },
  {
    meta: { title: 'My Orders', requiresAuth: true, role: 'freelancer' },
    path: '/freelancer/orders',
    name: 'freelancer-orders',
    component: () => import('@/views/freelancer/MyOrdersView.vue'),
  },

  // CLIENT ROUTES
  {
    meta: { title: 'My Dashboard', requiresAuth: true, role: 'client' },
    path: '/client/dashboard',
    name: 'client-dashboard',
    component: () => import('@/views/client/DashboardView.vue'),
  },
  {
    meta: { title: 'Browse Services', requiresAuth: true, role: 'client' },
    path: '/client/browse',
    name: 'client-browse',
    component: () => import('@/views/client/BrowseServicesView.vue'),
  },
  {
    meta: { title: 'My Orders', requiresAuth: true, role: 'client' },
    path: '/client/orders',
    name: 'client-orders',
    component: () => import('@/views/client/MyOrdersView.vue'),
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { top: 0 }
  },
})

// Route Home by Role
const roleHome = {
  admin: 'dashboard',
  freelancer: 'freelancer-dashboard',
  client: 'client-dashboard',
}

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const role = authStore.user?.role

  // 1. Logged-in user tries to visit the login page  send to their dashboard
  if (to.meta.guestOnly && authStore.isAuthenticated) {
    return next({ name: roleHome[role] || 'login' })
  }

  // 2. Route requires authentication
  if (to.meta.requiresAuth) {
    // Not authenticated → redirect to login
    if (!authStore.isAuthenticated) {
      return next({ name: 'login' })
    }

    // Route has a required role and user doesn't match → redirect to their home
    if (to.meta.role && to.meta.role !== role) {
      return next({ name: roleHome[role] || 'login' })
    }
  }

  next()
})

export default router
