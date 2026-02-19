<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();

// This runs EVERY time the template re-renders (even if nothing changed)
const welcomeMessage = computed(() => {
  if (authStore.isClient) {
    return `Welcome back, ${authStore.user?.name}! Find services that match your needs.`;
  } else if (authStore.isFreelancer) {
    return `Welcome back, ${authStore.user?.name}! Manage your services and orders.`;
  }
  return `Welcome back, ${authStore.user?.name}!`;
});

const quickActions = computed(() => {
  if (authStore.isClient) {
    return [
      { title: 'Browse Services', to: '/services', icon: '🔍', color: 'blue' },
      { title: 'My Orders', to: '/orders', icon: '📦', color: 'green' },
      { title: 'Profile', to: '/profile', icon: '👤', color: 'purple' }
    ];
  } else if (authStore.isFreelancer) {
    return [
      { title: 'My Services', to: '/my-services', icon: '💼', color: 'blue' },
      { title: 'My Orders', to: '/orders', icon: '📦', color: 'green' },
      { title: 'Create Service', to: '/services/create', icon: '➕', color: 'purple' }
    ];
  }
  return [];
});
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-600">Welcome, {{ authStore.user?.name }}!</span>
          <button 
            @click="authStore.logout()" 
            class="text-sm text-red-600 hover:text-red-800 font-medium"
          >
            Logout
          </button>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Welcome Section -->
      <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-8 text-white mb-8">
        <h2 class="text-3xl font-bold mb-2">Legal Marketplace</h2>
        <p class="text-lg opacity-90">{{ welcomeMessage }}</p>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div 
          v-for="action in quickActions" 
          :key="action.title"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition cursor-pointer"
          @click="$router.push(action.to)"
        >
          <div class="flex items-center">
            <span class="text-3xl mr-4">{{ action.icon }}</span>
            <div>
              <h3 class="font-semibold text-lg text-gray-900">{{ action.title }}</h3>
              <p class="text-gray-600 text-sm mt-1">Click to access</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="rounded-full bg-blue-100 p-3 mr-4">
              <span class="text-2xl">💼</span>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Total Services</p>
              <p class="text-2xl font-bold">24</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="rounded-full bg-green-100 p-3 mr-4">
              <span class="text-2xl">📦</span>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Active Orders</p>
              <p class="text-2xl font-bold">8</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="rounded-full bg-yellow-100 p-3 mr-4">
              <span class="text-2xl">⭐</span>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Rating</p>
              <p class="text-2xl font-bold">4.8</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="rounded-full bg-purple-100 p-3 mr-4">
              <span class="text-2xl">💰</span>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Earnings</p>
              <p class="text-2xl font-bold">$2,450</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>