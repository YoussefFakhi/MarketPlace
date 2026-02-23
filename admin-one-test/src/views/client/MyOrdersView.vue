<script setup>
import { computed, ref, onMounted } from 'vue'
import { useOrderStore } from '@/stores/order'
import { mdiCartOutline, mdiEye } from '@mdi/js'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionMain from '@/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import CardBox from '@/components/CardBox.vue'
import CardBoxModal from '@/components/CardBoxModal.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import BaseLevel from '@/components/BaseLevel.vue'
import PillTag from '@/components/PillTag.vue'

const orderStore = useOrderStore()
onMounted(() => orderStore.fetchOrders())

const orders = computed(() => orderStore.orders)
const isLoading = computed(() => orderStore.loading)
const currentPage = computed(() => orderStore.pagination.current_page)
const numPages = computed(() => orderStore.pagination.last_page)
const pagesList = computed(() => {
  const list = []
  for (let i = 1; i <= numPages.value; i++) list.push(i)
  return list
})

const currentOrder = ref(null)
const isDetailModalActive = ref(false)

const viewOrder = (order) => {
  currentOrder.value = order
  isDetailModalActive.value = true
}

const getStatusColor = (status) => {
  const map = { pending: 'warning', in_progress: 'info', completed: 'success', cancelled: 'danger' }
  return map[status] || 'info'
}

const getStatusMessage = (status) => {
  const map = {
    pending: 'Waiting for freelancer to start...',
    in_progress: ' Freelancer is working on your order!',
    completed: 'Order delivered. Enjoy your service!',
    cancelled: 'This order was cancelled.'
  }
  return map[status] || ''
}
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiCartOutline" title="My Orders" main />

      <!-- Order Detail Modal -->
      <CardBoxModal v-model="isDetailModalActive" :title="`Order #${currentOrder?.id}`" button="info">
        <div v-if="currentOrder" class="space-y-4 text-sm">
          <div class="p-3 rounded-lg bg-gray-50 dark:bg-slate-800 text-center font-medium">
            {{ getStatusMessage(currentOrder.status) }}
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <p class="text-gray-400 text-xs mb-1">Service</p>
              <p class="font-semibold">{{ currentOrder.service?.title }}</p>
            </div>
            <div>
              <p class="text-gray-400 text-xs mb-1">Freelancer</p>
              <p class="font-semibold">{{ currentOrder.freelancer?.name }}</p>
            </div>
            <div>
              <p class="text-gray-400 text-xs mb-1">Amount Paid</p>
              <p class="font-bold text-emerald-500 text-lg">${{ currentOrder.montant }}</p>
            </div>
            <div>
              <p class="text-gray-400 text-xs mb-1">Order Date</p>
              <p>{{ new Date(currentOrder.created_at).toLocaleDateString() }}</p>
            </div>
          </div>
          <div class="flex items-center justify-between border-t pt-3 dark:border-slate-700">
            <span class="text-gray-500">Status</span>
            <PillTag :color="getStatusColor(currentOrder.status)" :label="currentOrder.status" />
          </div>
        </div>
      </CardBoxModal>

      <CardBox has-table>
        <table :class="{ 'opacity-50 pointer-events-none': isLoading }">
          <thead>
            <tr>
              <th>Order #</th>
              <th>Service</th>
              <th>Freelancer</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Date</th>
              <th />
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id">
              <td data-label="Order #" class="font-mono text-gray-500">#{{ order.id }}</td>
              <td data-label="Service" class="font-medium">{{ order.service?.title }}</td>
              <td data-label="Freelancer">{{ order.freelancer?.name }}</td>
              <td data-label="Amount" class="font-bold text-emerald-500">${{ order.montant }}</td>
              <td data-label="Status">
                <PillTag :color="getStatusColor(order.status)" :label="order.status" small />
              </td>
              <td data-label="Date">
                <small class="text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</small>
              </td>
              <td class="before:hidden lg:w-1">
                <BaseButton color="info" :icon="mdiEye" small @click="viewOrder(order)" />
              </td>
            </tr>
            <tr v-if="orders.length === 0 && !isLoading">
              <td colspan="7" class="p-8 text-center text-gray-400">
                You haven't placed any orders yet.
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="isLoading" class="p-6 text-center text-blue-500 animate-pulse">Loading...</div>
        <div v-else class="border-t border-gray-100 p-3 lg:px-6 dark:border-slate-800">
          <BaseLevel>
            <BaseButtons>
              <BaseButton
                v-for="page in pagesList" :key="page"
                :active="page === currentPage" :label="page"
                :color="page === currentPage ? 'lightDark' : 'whiteDark'"
                small :disabled="isLoading"
                @click="orderStore.fetchOrders(page)"
              />
            </BaseButtons>
            <small>Page {{ currentPage }} of {{ numPages }}</small>
          </BaseLevel>
        </div>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>
