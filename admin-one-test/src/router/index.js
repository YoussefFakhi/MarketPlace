import { createRouter, createWebHashHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Home from '@/views/HomeView.vue'

const routes = [
  {
    path: '/',
    redirect: '/dashboard',
  },
  {
    meta: {
      title: 'Dashboard',
      requiresAuth: true
    },
    path: '/dashboard',
    name: 'dashboard',
    component: Home,
  },
  {
    meta: {
      title: 'Profile',
      requiresAuth: true
    },
    path: '/profile',
    name: 'profile',
    component: () => import('@/views/ProfileView.vue'),
  },
  {
    meta: {
      title: 'Login',
      guestOnly: true
    },
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
  },
  {
    meta: {
      title: 'Services',
      requiresAuth: true
    },
    path: '/services-moderation',
    name: 'services-moderation',
    component: () => import('@/views/ServicesView.vue'),
  },
  {
    meta: {
      title: 'Categories',
      requiresAuth: true
    },
    path: '/categories',
    name: 'categories',
    component: () => import('@/views/CategoriesView.vue'),
  },
  {
    meta: {
      title: 'Orders',
      requiresAuth: true
    },
    path: '/orders-monitoring',
    name: 'orders-monitoring',
    component: () => import('@/views/OrdersView.vue'),
  },
  {
    meta: {
      title: 'Users',
      requiresAuth: true
    },
    path: '/users-management',
    name: 'users-management',
    component: () => import('@/views/UsersView.vue'),
  },
  {
    meta: {
      title: 'Error',
    },
    path: '/error',
    name: 'error',
    component: () => import('@/views/ErrorView.vue'),
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { top: 0 }
  },
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // 1. If user is logged in and tries to go to Login/Style page, send to Dashboard
  if (to.meta.guestOnly && authStore.isAuthenticated) {
    return next({ name: 'dashboard' })
  }

  // 2. If route requires auth
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      return next({ name: 'login' })
    }

    // 3. Strict Admin Role Check
    if (authStore.user?.role !== 'admin') {
      alert('Access Denied: Admin privileges required.')
      authStore.logout()
      return next({ name: 'login' })
    }
  }

  next()
})

export default router
