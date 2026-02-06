<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import Navigation from './components/Navigation.vue'

const authStore = useAuthStore()

// Initialize user on app load
onMounted(() => {
  authStore.fetchUser()
})
</script>

<template>
  <div style="min-height: 100vh; background-color: #f9fafb;">
    <Navigation />
    <main style="max-width: 1200px; margin: 0 auto; padding: 0 16px; padding-top: 32px;">
      <router-view />
    </main>
  </div>
</template>