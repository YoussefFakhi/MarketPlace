<!-- src/components/Navigation.vue -->
<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Computed property to show user info when logged in
const userInfo = computed(() => {
  if (authStore.isLoggedIn) {
    return `${authStore.userName} (${authStore.role})`
  }
  return 'Guest'
})

// Handle logout
const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login') // Redirect to login after logout
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>

<template>
  <nav style="background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 16px;">
      <div style="display: flex; justify-content: space-between; height: 64px; align-items: center;">
        <div style="display: flex; align-items: center;">
          <h1 style="font-size: 1.25rem; font-weight: bold; color: #2d3748;">Marketplace</h1>
        </div>
        
        <div style="display: flex; align-items: center; gap: 16px;">
          <!-- Show different links based on login status -->
          <template v-if="authStore.isLoggedIn">
            <span style="font-size: 0.875rem; color: #718096;">Welcome, {{ userInfo }}</span>
            <button 
              @click="handleLogout"
              :disabled="authStore.loading"
              style="
                padding: 8px 16px; 
                background-color: #ef4444; 
                color: white; 
                border-radius: 4px; 
                border: none; 
                cursor: pointer;
              "
            >
              {{ authStore.loading ? 'Logging out...' : 'Logout' }}
            </button>
          </template>
          
          <template v-else>
            <router-link 
              to="/login" 
              style="
                padding: 8px 16px; 
                color: #4a5568; 
                text-decoration: none;
              "
            >
              Login
            </router-link>
            <router-link 
              to="/register" 
              style="
                padding: 8px 16px; 
                background-color: #3b82f6; 
                color: white; 
                border-radius: 4px; 
                text-decoration: none;
              "
            >
              Register
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>