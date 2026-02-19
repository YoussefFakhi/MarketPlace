<script setup>
import { computed, onMounted, ref } from 'vue'
import { useOrderStore } from '@/stores/order'
import { mdiEye } from '@mdi/js'
import CardBoxModal from '@/components/CardBoxModal.vue'
import BaseLevel from '@/components/BaseLevel.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import BaseButton from '@/components/BaseButton.vue'
import PillTag from '@/components/PillTag.vue'

const orderStore = useOrderStore()
const isModalInfoActive = ref(false)
const currentOrder = ref(null)

onMounted(() => {
  orderStore.fetchOrders()
})

const orders = computed(() => orderStore.orders)
const isLoading = computed(() => orderStore.loading)

const currentPage = computed(() => orderStore.pagination.current_page)
const numPages = computed(() => orderStore.pagination.last_page)

const pagesList = computed(() => {
  const pagesList = []
  for (let i = 1; i <= numPages.value; i++) {
    pagesList.push(i)
  }
  return pagesList
})

const infoClick = (order) => {
  currentOrder.value = order
  isModalInfoActive.value = true
}

const getStatusColor = (status) => {
  switch (status) {
    case 'pending': return 'warning'
    case 'completed': return 'success'
    case 'cancelled': return 'danger'
    case 'in_progress': return 'info'
    default: return 'info'
  }
}
</script>

<template>
  <CardBoxModal
    v-model="isModalInfoActive"
    title="Order Details"
    has-cancel
  >
    <div v-if="currentOrder" class="space-y-3">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-xs uppercase text-gray-500 font-bold">Order ID</p>
          <p>#{{ currentOrder.id }}</p>
        </div>
        <div>
          <p class="text-xs uppercase text-gray-500 font-bold">Status</p>
          <PillTag :color="getStatusColor(currentOrder.status)" :label="currentOrder.status" small />
        </div>
      </div>

      <div class="border-t pt-2">
        <p class="text-xs uppercase text-gray-500 font-bold">Service</p>
        <p class="font-semibold">{{ currentOrder.service?.title }}</p>
      </div>

      <div class="grid grid-cols-2 gap-4 border-t pt-2">
        <div>
          <p class="text-xs uppercase text-gray-500 font-bold">Client</p>
          <p>{{ currentOrder.client?.name }}</p>
          <p class="text-xs text-gray-400">{{ currentOrder.client?.email }}</p>
        </div>
        <div>
          <p class="text-xs uppercase text-gray-500 font-bold">Freelancer</p>
          <p>{{ currentOrder.freelancer?.name }}</p>
          <p class="text-xs text-gray-400">{{ currentOrder.freelancer?.email }}</p>
        </div>
      </div>

      <div class="border-t pt-2">
        <p class="text-xs uppercase text-gray-500 font-bold">Amount</p>
        <p class="text-xl font-bold text-emerald-600">${{ currentOrder.montant }}</p>
      </div>

      <div class="border-t pt-2">
        <p class="text-xs uppercase text-gray-500 font-bold">Date</p>
        <p>{{ new Date(currentOrder.created_at).toLocaleString() }}</p>
      </div>
    </div>
  </CardBoxModal>

  <table :class="{ 'opacity-50 pointer-events-none transition-opacity duration-300': isLoading }">
    <thead>
      <tr>
        <th>ID</th>
        <th>Client</th>
        <th>Freelancer</th>
        <th>Service</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Date</th>
        <th />
      </tr>
    </thead>
    <tbody>
      <tr v-for="order in orders" :key="order.id">
        <td data-label="ID">#{{ order.id }}</td>
        <td data-label="Client">{{ order.client?.name }}</td>
        <td data-label="Freelancer">{{ order.freelancer?.name }}</td>
        <td data-label="Service" class="max-w-[150px] truncate">{{ order.service?.title }}</td>
        <td data-label="Amount" class="font-bold text-emerald-600">${{ order.montant }}</td>
        <td data-label="Status">
          <PillTag :color="getStatusColor(order.status)" :label="order.status" small />
        </td>
        <td data-label="Date">
          <small class="text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</small>
        </td>
        <td class="before:hidden lg:w-1 whitespace-nowrap">
          <BaseButtons type="justify-start lg:justify-end" no-wrap>
            <BaseButton color="info" :icon="mdiEye" small @click="infoClick(order)" />
          </BaseButtons>
        </td>
      </tr>
    </tbody>
  </table>

  <div v-if="orders.length === 0 && !isLoading" class="p-6 text-center text-gray-500">
    No orders recorded yet.
  </div>
  <div v-if="isLoading" class="p-6 text-center text-blue-500 font-bold animate-pulse">
    Loading data...
  </div>
  <div v-else class="border-t border-gray-100 p-3 lg:px-6 dark:border-slate-800">
    <BaseLevel>
      <BaseButtons>
        <BaseButton
          v-for="page in pagesList"
          :key="page"
          :active="page === currentPage"
          :label="page"
          :color="page === currentPage ? 'lightDark' : 'whiteDark'"
          :disabled="isLoading"
          small
          @click="orderStore.fetchOrders(page)"
        />
      </BaseButtons>
      <small>Page {{ currentPage }} of {{ numPages }}</small>
    </BaseLevel>
  </div>
</template>
