<script setup>
import { computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useOrderStore } from '@/stores/order'
import { useMyServiceStore } from '@/stores/myService'
import {
  mdiViewList,
  mdiClipboardListOutline,
  mdiCurrencyUsd,
  mdiCheckCircleOutline,
  mdiTimerSandFull,
  mdiAlertCircleOutline,
} from '@mdi/js'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionMain from '@/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import CardBoxWidget from '@/components/CardBoxWidget.vue'
import PillTag from '@/components/PillTag.vue'

const authStore = useAuthStore()
const orderStore = useOrderStore()
const myServiceStore = useMyServiceStore()

onMounted(() => {
  orderStore.fetchOrders()
  myServiceStore.fetchMyServices()
})

const totalServices = computed(() => myServiceStore.pagination.total)
const totalOrders = computed(() => orderStore.pagination.total)
const earnings = computed(() => {
  return orderStore.orders
    .filter(o => o.status === 'completed')
    .reduce((sum, o) => sum + parseFloat(o.montant || 0), 0)
    .toFixed(2)
})
const pendingOrders = computed(() => orderStore.orders.filter(o => o.status === 'pending').length)

const recentOrders = computed(() => orderStore.orders.slice(0, 5))

const getStatusColor = (status) => {
  const map = { pending: 'warning', in_progress: 'info', completed: 'success', cancelled: 'danger' }
  return map[status] || 'info'
}
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiViewList" title="Freelancer Dashboard" main>
        <span class="text-sm text-gray-500 dark:text-slate-400">
          Welcome back, <b>{{ authStore.user?.name }}</b>! 👋
        </span>
      </SectionTitleLineWithButton>

      <!-- Stats Widgets -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-4 mb-6">
        <CardBoxWidget
          trend="Active Services"
          trend-type="up"
          color="text-emerald-500"
          :icon="mdiViewList"
          :number="totalServices"
          label="My Services"
        />
        <CardBoxWidget
          trend="All Time"
          trend-type="up"
          color="text-blue-500"
          :icon="mdiClipboardListOutline"
          :number="totalOrders"
          label="Total Orders"
        />
        <CardBoxWidget
          trend="Completed Only"
          trend-type="up"
          color="text-yellow-500"
          :icon="mdiCurrencyUsd"
          :number="parseFloat(earnings)"
          prefix="$"
          label="Total Earnings"
        />
        <CardBoxWidget
          trend="Awaiting Action"
          trend-type="warning"
          color="text-orange-500"
          :icon="mdiTimerSandFull"
          :number="pendingOrders"
          label="Pending Orders"
        />
      </div>

      <!-- Recent Orders Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm">
        <div class="p-4 border-b dark:border-slate-700">
          <h2 class="font-semibold text-gray-700 dark:text-slate-300">Recent Orders</h2>
        </div>
        <div v-if="orderStore.loading" class="p-8 text-center text-blue-500 animate-pulse">Loading orders...</div>
        <table v-else class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500 dark:text-slate-400 border-b dark:border-slate-700">
              <th class="p-4">Order #</th>
              <th class="p-4">Service</th>
              <th class="p-4">Client</th>
              <th class="p-4">Amount</th>
              <th class="p-4">Status</th>
              <th class="p-4">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="order in recentOrders"
              :key="order.id"
              class="border-b dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors"
            >
              <td class="p-4 font-mono text-gray-600 dark:text-slate-400">#{{ order.id }}</td>
              <td class="p-4 font-medium">{{ order.service?.title }}</td>
              <td class="p-4">{{ order.client?.name }}</td>
              <td class="p-4 font-bold text-emerald-500">${{ order.montant }}</td>
              <td class="p-4">
                <PillTag :color="getStatusColor(order.status)" :label="order.status" small />
              </td>
              <td class="p-4 text-gray-500 text-xs">{{ new Date(order.created_at).toLocaleDateString() }}</td>
            </tr>
            <tr v-if="recentOrders.length === 0">
              <td colspan="6" class="p-8 text-center text-gray-400">No orders yet. Share your services to get started!</td>
            </tr>
          </tbody>
        </table>
      </div>
    </SectionMain>
  </LayoutAuthenticated>
</template>
