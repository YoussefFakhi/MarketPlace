<script setup>
import { RouterView } from 'vue-router';
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()

// Initialize user on app load
onMounted(() => {
  // Check if token exists and fetch user data
  if (authStore.token) {
    authStore.fetchUser().catch(() => {
      // If fetch fails, user might have invalid token
      authStore.logout();
    });
  }
});
</script>

<template>
  <RouterView />
</template>