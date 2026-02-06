<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Computed properties for user data
const user = computed(() => authStore.user)
const isLoggedIn = computed(() => authStore.isLoggedIn)
const loading = computed(() => authStore.loading)

// Check if user is authenticated, redirect if not
if (!isLoggedIn.value && !loading.value) {
  router.push('/login')
}
</script>

<template>
  <div v-if="loading" style="text-align: center; padding: 32px;">
    <p>Loading...</p>
  </div>
  
  <div v-else-if="isLoggedIn" style="
    max-width: 800px; 
    margin: 0 auto; 
    background-color: white; 
    padding: 32px; 
    border-radius: 8px; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  ">
    <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 24px;">Dashboard</h2>
    
    <div style="margin-top: 16px;">
      <div style="
        padding: 16px; 
        background-color: #f9fafb; 
        border-radius: 8px; 
        margin-bottom: 16px;
      ">
        <h3 style="
          font-size: 1.125rem; 
          font-weight: 600; 
          color: #4a5568; 
          margin-bottom: 8px;
        ">User Information</h3>
        <div style="margin-top: 8px;">
          <p><strong>Name:</strong> {{ user.name }}</p>
          <p><strong>Email:</strong> {{ user.email }}</p>
          <p><strong>Role:</strong> {{ user.role }}</p>
          <p><strong>User ID:</strong> {{ user.id }}</p>
        </div>
      </div>
      
      <div style="
        padding: 16px; 
        background-color: #eff6ff; 
        border-radius: 8px;
      ">
        <h3 style="
          font-size: 1.125rem; 
          font-weight: 600; 
          color: #1e40af; 
          margin-bottom: 8px;
        ">Welcome!</h3>
        <p style="color: #2563eb;">
          You are successfully logged in as a {{ user.role }}. 
          This is your personalized dashboard.
        </p>
      </div>
    </div>
  </div>
  
  <div v-else style="text-align: center; padding: 32px;">
    <p style="color: #718096;">Please log in to access the dashboard.</p>
  </div>
</template>