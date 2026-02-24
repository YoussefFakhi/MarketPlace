<script setup>
import { computed, ref, onMounted } from 'vue'
import { useOrderStore } from '@/stores/order'
import { mdiClipboardListOutline, mdiCheckCircle, mdiProgressClock, mdiCancel } from '@mdi/js'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionMain from '@/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import CardBox from '@/components/CardBox.vue'
import CardBoxModal from '@/components/CardBoxModal.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import BaseLevel from '@/components/BaseLevel.vue'
import PillTag from '@/components/PillTag.vue'
import NotificationBar from '@/components/NotificationBar.vue'

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
const message = ref(null)

const viewOrder = (order) => {
  currentOrder.value = order
  isDetailModalActive.value = true
}

const updateStatus = async (order, status) => {
  try {
    await orderStore.updateOrderStatus(order.id, status)
    if (currentOrder.value?.id === order.id) {
      currentOrder.value = { ...currentOrder.value, status }
    }
    message.value = { type: 'success', text: `Order marked as "${status}"` }
  } catch (error) {
    message.value = { type: 'danger', text: error }
  }
}

const getStatusColor = (status) => {
  const map = { pending: 'warning', in_progress: 'info', completed: 'success', cancelled: 'danger' }
  return map[status] || 'info'
}

const canProgress = (status) => status === 'pending'
const canComplete = (status) => status === 'in_progress'
const canCancel = (status) => status === 'pending' || status === 'in_progress'
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiClipboardListOutline" title="My Orders" main />

      <NotificationBar v-if="message" :color="message.type" @dismiss="message = null">
        {{ message.text }}
      </NotificationBar>

      <!-- Order Detail Modal -->
      <CardBoxModal v-model="isDetailModalActive" title="Order Details" button="info">
        <div v-if="currentOrder" class="space-y-3 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-500">Order #</span>
            <span class="font-mono font-bold">#{{ currentOrder.id }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Service</span>
            <span class="font-medium">{{ currentOrder.service?.title }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Client</span>
            <span>{{ currentOrder.client?.name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Amount</span>
            <span class="font-bold text-emerald-500">${{ currentOrder.montant }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-500">Status</span>
            <PillTag :color="getStatusColor(currentOrder.status)" :label="currentOrder.status" small />
          </div>
          <div class="flex gap-2 pt-3 border-t dark:border-slate-700">
            <BaseButton
              v-if="canProgress(currentOrder.status)"
              color="info" label="Mark In Progress" :icon="mdiProgressClock" small
              @click="updateStatus(currentOrder, 'in_progress')"
            />
            <BaseButton
              v-if="canComplete(currentOrder.status)"
              color="success" label="Mark Completed" :icon="mdiCheckCircle" small
              @click="updateStatus(currentOrder, 'completed')"
            />
            <BaseButton
              v-if="canCancel(currentOrder.status)"
              color="danger" label="Cancel Order" :icon="mdiCancel" small
              @click="updateStatus(currentOrder, 'cancelled')"
            />
          </div>
        </div>
      </CardBoxModal>

      <CardBox has-table>
        <table :class="{ 'opacity-50 pointer-events-none': isLoading }">
          <thead>
            <tr>
              <th>Order #</th>
              <th>Service</th>
              <th>Client</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Date</th>
              <th />
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id">
              <td data-label="Order #" class="font-mono">#{{ order.id }}</td>
              <td data-label="Service" class="font-medium">{{ order.service?.title }}</td>
              <td data-label="Client">{{ order.client?.name }}</td>
              <td data-label="Amount" class="font-bold text-emerald-500">${{ order.montant }}</td>
              <td data-label="Status">
                <PillTag :color="getStatusColor(order.status)" :label="order.status" small />
              </td>
              <td data-label="Date">
                <small class="text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</small>
              </td>
              <td class="before:hidden lg:w-1 whitespace-nowrap">
                <BaseButtons type="justify-start lg:justify-end" no-wrap>
                  <BaseButton color="info" label="View" small @click="viewOrder(order)" />
                  <BaseButton
                    v-if="canProgress(order.status)" color="warning" label="Start" small
                    @click="updateStatus(order, 'in_progress')"
                  />
                  <BaseButton
                    v-if="canComplete(order.status)" color="success" label="Complete" small
                    @click="updateStatus(order, 'completed')"
                  />
                  <BaseButton
                    v-if="canCancel(order.status)" color="danger" label="Cancel" small
                    @click="updateStatus(order, 'cancelled')"
                  />
                </BaseButtons>
              </td>
            </tr>
            <tr v-if="orders.length === 0 && !isLoading">
              <td colspan="7" class="p-8 text-center text-gray-400">You have no orders yet.</td>
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
