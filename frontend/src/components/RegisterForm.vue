<!-- src/components/RegisterForm.vue -->
<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Form data
const formData = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'client' // Default role
})

// Loading state from store
const loading = computed(() => authStore.loading)
const error = computed(() => authStore.error)

// Handle form submission
const handleSubmit = async (event) => {
  event.preventDefault()
  
  // Validate passwords match
  if (formData.value.password !== formData.value.password_confirmation) {
    authStore.error = 'Passwords do not match'
    return
  }
  
  try {
    await authStore.register(formData.value)
    
    // Redirect to dashboard after successful registration
    router.push('/dashboard')
  } catch (error) {
    console.error('Registration error:', error)
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
    ">Register</h2>
    
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
        <label for="name" style="
          display: block; 
          font-size: 0.875rem; 
          font-weight: 500; 
          color: #4a5568; 
          margin-bottom: 4px;
        ">
          Name
        </label>
        <input
          id="name"
          v-model="formData.name"
          type="text"
          required
          style="
            width: 100%; 
            padding: 8px 12px; 
            border: 1px solid #e2e8f0; 
            border-radius: 4px; 
            outline: none;
          "
          placeholder="Enter your name"
        />
      </div>
      
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
          minlength="8"
          style="
            width: 100%; 
            padding: 8px 12px; 
            border: 1px solid #e2e8f0; 
            border-radius: 4px; 
            outline: none;
          "
          placeholder="Enter your password (min 8 chars)"
        />
      </div>
      
      <div style="margin-bottom: 16px;">
        <label for="password_confirmation" style="
          display: block; 
          font-size: 0.875rem; 
          font-weight: 500; 
          color: #4a5568; 
          margin-bottom: 4px;
        ">
          Confirm Password
        </label>
        <input
          id="password_confirmation"
          v-model="formData.password_confirmation"
          type="password"
          required
          style="
            width: 100%; 
            padding: 8px 12px; 
            border: 1px solid #e2e8f0; 
            border-radius: 4px; 
            outline: none;
          "
          placeholder="Confirm your password"
        />
      </div>
      
      <div style="margin-bottom: 16px;">
        <label for="role" style="
          display: block; 
          font-size: 0.875rem; 
          font-weight: 500; 
          color: #4a5568; 
          margin-bottom: 4px;
        ">
          Role
        </label>
        <select
          id="role"
          v-model="formData.role"
          style="
            width: 100%; 
            padding: 8px 12px; 
            border: 1px solid #e2e8f0; 
            border-radius: 4px; 
            outline: none;
          "
        >
          <option value="client">Client</option>
          <option value="freelancer">Freelancer</option>
        </select>
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
          background-color: #16a34a; 
          cursor: pointer;
        "
      >
        {{ loading ? 'Registering...' : 'Register' }}
      </button>
    </form>
    
    <div style="margin-top: 16px; text-align: center;">
      <p style="font-size: 0.875rem; color: #718096;">
        Already have an account?
        <router-link to="/login" style="
          font-weight: 500; 
          color: #2563eb; 
          text-decoration: none;
        ">
          Login here
        </router-link>
      </p>
    </div>
  </div>
</template>