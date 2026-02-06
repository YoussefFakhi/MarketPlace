<!-- src/components/LoginForm.vue -->
<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Form data
const formData = ref({
  email: '',
  password: ''
})

// Loading state from store
const loading = computed(() => authStore.loading)
const error = computed(() => authStore.error)

// Handle form submission
const handleSubmit = async (event) => {
  event.preventDefault()
  
  try {
    await authStore.login(formData.value)
    
    // Redirect to dashboard after successful login
    router.push('/dashboard')
  } catch (error) {
    console.error('Login error:', error)
  }
}
</script>

<template>
  <div style="
    max-width: 400px; 
    margin: 0 auto; 
    background-color: white; 
    padding: 32px; 
    border-radius: 8px; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  ">
    <h2 style="
      font-size: 1.5rem; 
      font-weight: bold; 
      margin-bottom: 24px; 
      text-align: center;
    ">Login</h2>
    
    <!-- Error display -->
    <div v-if="error" style="
      margin-bottom: 16px; 
      padding: 12px; 
      background-color: #fee2e2; 
      color: #dc2626; 
      border-radius: 4px;
    ">
      {{ error }}
    </div>
    
    <form @submit="handleSubmit" style="margin-top: 16px;">
      <div style="margin-bottom: 16px;">
        <label for="email" style="
          display: block; 
          font-size: 0.875rem; 
          font-weight: 500; 
          color: #4a5568; 
          margin-bottom: 4px;
        ">
          Email
        </label>
        <input
          id="email"
          v-model="formData.email"
          type="email"
          required
          style="
            width: 100%; 
            padding: 8px 12px; 
            border: 1px solid #e2e8f0; 
            border-radius: 4px; 
            outline: none;
          "
          placeholder="Enter your email"
        />
      </div>
      
      <div style="margin-bottom: 16px;">
        <label for="password" style="
          display: block; 
          font-size: 0.875rem; 
          font-weight: 500; 
          color: #4a5568; 
          margin-bottom: 4px;
        ">
          Password
        </label>
        <input
          id="password"
          v-model="formData.password"
          type="password"
          required
          style="
            width: 100%; 
            padding: 8px 12px; 
            border: 1px solid #e2e8f0; 
            border-radius: 4px; 
            outline: none;
          "
          placeholder="Enter your password"
        />
      </div>
      
      <button
        type="submit"
        :disabled="loading"
        style="
          width: 100%; 
          display: flex; 
          justify-content: center; 
          padding: 8px 16px; 
          border: none; 
          border-radius: 4px; 
          font-size: 0.875rem; 
          font-weight: 500; 
          color: white; 
          background-color: #2563eb; 
          cursor: pointer;
        "
      >
        {{ loading ? 'Logging in...' : 'Login' }}
      </button>
    </form>
    
    <div style="margin-top: 16px; text-align: center;">
      <p style="font-size: 0.875rem; color: #718096;">
        Don't have an account?
        <router-link to="/register" style="
          font-weight: 500; 
          color: #2563eb; 
          text-decoration: none;
        ">
          Register here
        </router-link>
      </p>
    </div>
  </div>
</template>