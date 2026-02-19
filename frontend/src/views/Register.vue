<!-- src/views/Register.vue -->
<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const form = ref({ 
  name: '', 
  email: '', 
  password: '', 
  password_confirmation: '',
  role: 'client' // Default role
});
const error = ref(null);
const loading = ref(false);

const handleRegister = async () => {
  loading.value = true;
  error.value = null;

  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Passwords do not match';
    loading.value = false;
    return;
  }

  try {
    await authStore.register(form.value);
    
    // Redirect based on selected role
    switch(form.value.role) {
      case 'client':
        await router.push('/dashboard');
        break;
      case 'freelancer':
        await router.push('/my-services');
        break;
      default:
        await router.push('/dashboard');
    }
  } catch (err) {
    error.value = typeof err === 'string' ? err : 'Registration failed. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 to-teal-100">
    <form @submit.prevent="handleRegister" class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
      <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Join Marketplace</h2>
      
      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ error }}
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm font-semibold mb-2 text-gray-700">Full Name</label>
          <input 
            v-model="form.name" 
            type="text" 
            class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="John Doe"
            required 
          />
        </div>

        <div>
          <label class="block text-sm font-semibold mb-2 text-gray-700">Role</label>
          <select 
            v-model="form.role"
            class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
          >
            <option value="client">Client</option>
            <option value="freelancer">Freelancer</option>
          </select>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-semibold mb-2 text-gray-700">Email</label>
        <input 
          v-model="form.email" 
          type="email" 
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
          placeholder="your@email.com"
          required 
        />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
          <label class="block text-sm font-semibold mb-2 text-gray-700">Password</label>
          <input 
            v-model="form.password" 
            type="password" 
            class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="••••••••"
            required 
          />
        </div>

        <div>
          <label class="block text-sm font-semibold mb-2 text-gray-700">Confirm Password</label>
          <input 
            v-model="form.password_confirmation" 
            type="password" 
            class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="••••••••"
            required 
          />
        </div>
      </div>

      <button 
        :disabled="loading" 
        class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 disabled:bg-gray-400 transition duration-200"
      >
        {{ loading ? 'Creating Account...' : 'Create Account' }}
      </button>
      
      <p class="mt-6 text-center text-sm text-gray-600">
        Already have an account? 
        <router-link to="/login" class="text-blue-600 hover:text-blue-800 font-medium">
            Sign in here
        </router-link>
      </p>
    </form>
  </div>
</template>