<script setup>
import { computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useOrderStore } from '@/stores/order'
import { useServiceStore } from '@/stores/service'
import {
  mdiMonitor, mdiCartOutline, mdiCurrencyUsd, mdiMagnify, mdiTimerSandFull,
} from '@mdi/js'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionMain from '@/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import CardBoxWidget from '@/components/CardBoxWidget.vue'
import BaseButton from '@/components/BaseButton.vue'
import PillTag from '@/components/PillTag.vue'
import { RouterLink } from 'vue-router'

const authStore = useAuthStore()
const orderStore = useOrderStore()
const serviceStore = useServiceStore()

onMounted(() => {
  orderStore.fetchOrders()
  serviceStore.fetchServices()
})

const totalOrders = computed(() => orderStore.pagination.total)
const totalSpent = computed(() => {
  return orderStore.orders
    .filter(o => o.status === 'completed')
    .reduce((sum, o) => sum + parseFloat(o.montant || 0), 0)
    .toFixed(2)
})
const pendingOrders = computed(() => orderStore.orders.filter(o => o.status === 'pending').length)
const recentOrders = computed(() => orderStore.orders.slice(0, 4))

const getStatusColor = (status) => {
  const map = { pending: 'warning', in_progress: 'info', completed: 'success', cancelled: 'danger' }
  return map[status] || 'info'
}
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiMonitor" title="My Dashboard" main>
        <span class="text-sm text-gray-500 dark:text-slate-400">
          Welcome back, <b>{{ authStore.user?.name }}</b>! 👋
        </span>
      </SectionTitleLineWithButton>

      <!-- Stat Widgets -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
        <CardBoxWidget
          trend="All Time"
          trend-type="up"
          color="text-blue-500"
          :icon="mdiCartOutline"
          :number="totalOrders"
          label="Total Orders"
        />
        <CardBoxWidget
          trend="Completed Orders"
          trend-type="up"
          color="text-emerald-500"
          :icon="mdiCurrencyUsd"
          :number="parseFloat(totalSpent)"
          prefix="$"
          label="Total Spent"
        />
        <CardBoxWidget
          trend="Awaiting Delivery"
          trend-type="warning"
          color="text-orange-500"
          :icon="mdiTimerSandFull"
          :number="pendingOrders"
          label="Pending Orders"
        />
      </div>

      <!-- Quick Actions -->
      <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 mb-6 flex items-center justify-between text-white">
        <div>
          <h3 class="text-xl font-bold">Looking for a service?</h3>
          <p class="text-blue-100 text-sm mt-1">Browse hundreds of professional services from talented freelancers.</p>
        </div>
        <RouterLink to="/client/browse">
          <BaseButton :icon="mdiMagnify" label="Browse Services" color="white" />
        </RouterLink>
      </div>

      <!-- Recent Orders -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm">
        <div class="p-4 border-b dark:border-slate-700 flex items-center justify-between">
          <h2 class="font-semibold text-gray-700 dark:text-slate-300">Recent Orders</h2>
          <RouterLink to="/client/orders" class="text-sm text-blue-500 hover:underline">View All →</RouterLink>
        </div>
        <div v-if="orderStore.loading" class="p-8 text-center text-blue-500 animate-pulse">Loading...</div>
        <table v-else class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500 dark:text-slate-400 border-b dark:border-slate-700">
              <th class="p-4">Order #</th>
              <th class="p-4">Service</th>
              <th class="p-4">Freelancer</th>
              <th class="p-4">Amount</th>
              <th class="p-4">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="order in recentOrders" :key="order.id"
              class="border-b dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors"
            >
              <td class="p-4 font-mono text-gray-500">#{{ order.id }}</td>
              <td class="p-4 font-medium">{{ order.service?.title }}</td>
              <td class="p-4">{{ order.freelancer?.name }}</td>
              <td class="p-4 font-bold text-emerald-500">${{ order.montant }}</td>
              <td class="p-4">
                <PillTag :color="getStatusColor(order.status)" :label="order.status" small />
              </td>
            </tr>
            <tr v-if="recentOrders.length === 0">
              <td colspan="5" class="p-8 text-center text-gray-400">
                No orders yet.
                <RouterLink to="/client/browse" class="text-blue-500 hover:underline ml-1">Browse services →</RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </SectionMain>
  </LayoutAuthenticated>
</template>
