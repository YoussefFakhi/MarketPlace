<script setup>
    import {ref} from 'vue';
    import { useAuthStore } from '../stores/auth';
    import { useRouter } from 'vue-router';

    const authStore = useAuthStore();
    const router = useRouter();

    const form = ref({email: '', password: ''});
    const error = ref(null);
    const loading = ref(false);

    const handleLogin = async ()=>{
        loading.value = true;
        error.value = null;

    try {
        await authStore.login(form.value);
        // Redirect based on user role
        switch(authStore.user.role) {
        case 'client':
            await router.push('/dashboard');
            break;
        case 'freelancer':
            await router.push('/my-services');
            break;
        case 'admin':
            await router.push('/admin');
            break;
        default:
            await router.push('/dashboard');
        }
    } catch (err) {
        error.value = typeof err === 'string' ? err : 'Login failed. Please check your credentials.';
    } finally {
        loading.value = false;
    }
    };

</script>


<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
    <form @submit.prevent="handleLogin" class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
      <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Legal Marketplace</h2>
      
      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ error }}
      </div>

      <div class="mb-6">
        <label class="block text-sm font-semibold mb-2 text-gray-700">Email</label>
        <input 
          v-model="form.email" 
          type="email" 
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
          placeholder="Enter your email"
          required 
        />
      </div>

      <div class="mb-6">
        <label class="block text-sm font-semibold mb-2 text-gray-700">Password</label>
        <input 
          v-model="form.password" 
          type="password" 
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
          placeholder="Enter your password"
          required 
        />
      </div>

      <button 
        :disabled="loading" 
        class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 disabled:bg-gray-400 transition duration-200"
      >
        {{ loading ? 'Signing in...' : 'Sign In' }}
      </button>
      
      <p class="mt-6 text-center text-sm text-gray-600">
        Don't have an account? 
        <router-link to="/register" class="text-blue-600 hover:text-blue-800 font-medium">
          Register here
        </router-link>
      </p>
    </form>
  </div>
</template>