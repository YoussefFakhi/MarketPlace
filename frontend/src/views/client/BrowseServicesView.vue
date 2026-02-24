<script setup>
import { ref, computed, onMounted } from 'vue'
import { useServiceStore } from '@/stores/service'
import { useCategoryStore } from '@/stores/category'
import api from '@/plugins/axios'
import { mdiMagnify, mdiCartOutline} from '@mdi/js'  //mdiFilterOutline
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionMain from '@/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseButtons from '@/components/BaseButtons.vue'
// import BaseLevel from '@/components/BaseLevel.vue'
import CardBoxModal from '@/components/CardBoxModal.vue'
import NotificationBar from '@/components/NotificationBar.vue'
import PillTag from '@/components/PillTag.vue'

const serviceStore = useServiceStore()
const categoryStore = useCategoryStore()

const selectedCategory = ref('')
const searchQuery = ref('')
const message = ref(null)
const selectedService = ref(null)
const isOrderModalActive = ref(false)
const isDetailModalActive = ref(false)
const isOrdering = ref(false)

onMounted(() => {
  serviceStore.fetchServices()
  categoryStore.fetchCategories()
})

const services = computed(() => {
  return serviceStore.services.filter(s => {
    const matchesSearch = !searchQuery.value ||
      s.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      s.description?.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchesSearch
  })
})

const isLoading = computed(() => serviceStore.loading)
const currentPage = computed(() => serviceStore.pagination.current_page)
const numPages = computed(() => serviceStore.pagination.last_page)
const pagesList = computed(() => {
  const list = []
  for (let i = 1; i <= numPages.value; i++) list.push(i)
  return list
})

const filterByCategory = (categoryId) => {
  selectedCategory.value = categoryId
  const params = categoryId ? { category_id: categoryId } : {}
  serviceStore.fetchServices(1, params)
}

const openDetail = (service) => {
  selectedService.value = service
  isDetailModalActive.value = true
}

const openOrderModal = (service) => {
  selectedService.value = service
  isOrderModalActive.value = true
  isDetailModalActive.value = false
}

const confirmOrder = async () => {
  if (!selectedService.value) return
  isOrdering.value = true
  try {
    await api.post('/orders', { service_id: selectedService.value.id })
    message.value = { type: 'success', text: `Order placed for "${selectedService.value.title}"! Check My Orders for updates.` }
  } catch (error) {
    message.value = {
      type: 'danger',
      text: error.response?.data?.message || 'Failed to place order. Please try again.'
    }
  } finally {
    isOrdering.value = false
  }
}
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiMagnify" title="Browse Services" main />

      <NotificationBar v-if="message" :color="message.type" @dismiss="message = null">
        {{ message.text }}
      </NotificationBar>

      <!-- Service Detail Modal -->
      <CardBoxModal v-model="isDetailModalActive" :title="selectedService?.title || ''" button="success" button-label="Order Now" has-cancel @confirm="openOrderModal(selectedService)">
        <div v-if="selectedService" class="space-y-3 text-sm">
          <p class="text-gray-600 dark:text-slate-300">{{ selectedService.description }}</p>
          <div class="flex justify-between items-center border-t pt-3 dark:border-slate-700">
            <span class="text-gray-500">Category</span>
            <PillTag color="info" :label="selectedService.category?.name || 'N/A'" small />
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-500">Freelancer</span>
            <span class="font-medium">{{ selectedService.user?.name }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-500">Price</span>
            <span class="text-2xl font-black text-emerald-500">${{ selectedService.price }}</span>
          </div>
        </div>
      </CardBoxModal>

      <!-- Order Confirmation Modal -->
      <CardBoxModal v-model="isOrderModalActive" title="Confirm Your Order" button="success" button-label="Yes, Place Order!" has-cancel @confirm="confirmOrder">
        <p v-if="selectedService">
          You are about to order <b>{{ selectedService.title }}</b> for
          <b class="text-emerald-500">${{ selectedService.price }}</b>.
        </p>
        <p class="text-sm text-gray-500 mt-2">The freelancer will be notified and your order will appear in "My Orders".</p>
      </CardBoxModal>

      <!-- Search + Filter Bar -->
      <div class="flex flex-wrap gap-3 mb-6">
        <div class="flex-1 min-w-48 relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search services..."
            class="w-full border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2 pl-9 text-sm bg-white dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <span class="absolute left-3 top-2.5 text-gray-400"></span>
        </div>
        <select
          v-model="selectedCategory"
          class="border border-gray-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="filterByCategory(selectedCategory)"
        >
          <option value="">All Categories</option>
          <option v-for="cat in categoryStore.categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>

      <!-- Service Cards Grid -->
      <div v-if="isLoading" class="p-12 text-center text-blue-500 animate-pulse text-lg">
        Loading services...
      </div>
      <div v-else-if="services.length === 0" class="p-12 text-center text-gray-400">
        No services found. Try a different search.
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-6">
        <div
          v-for="service in services" :key="service.id"
          class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 hover:shadow-md hover:-translate-y-1 transition-all duration-200 cursor-pointer flex flex-col"
          @click="openDetail(service)"
        >
          <div class="flex items-start justify-between mb-3">
            <PillTag color="info" :label="service.category?.name || 'N/A'" small />
            <span class="text-2xl font-black text-emerald-500">${{ service.price }}</span>
          </div>
          <h3 class="font-bold text-gray-800 dark:text-slate-100 mb-2 line-clamp-2">{{ service.title }}</h3>
          <p class="text-sm text-gray-500 dark:text-slate-400 flex-1 line-clamp-3">{{ service.description }}</p>
          <div class="mt-4 pt-4 border-t dark:border-slate-700 flex items-center justify-between">
            <span class="text-xs text-gray-400">By <b class="text-gray-600 dark:text-slate-300">{{ service.user?.name }}</b></span>
            <BaseButton :icon="mdiCartOutline" label="Order" color="success" small @click.stop="openOrderModal(service)" />
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center">
        <BaseButtons>
          <BaseButton
            v-for="page in pagesList" :key="page"
            :active="page === currentPage" :label="page"
            :color="page === currentPage ? 'lightDark' : 'whiteDark'"
            small :disabled="isLoading"
            @click="serviceStore.fetchServices(page, selectedCategory ? { category_id: selectedCategory } : {})"
          />
        </BaseButtons>
      </div>
    </SectionMain>
  </LayoutAuthenticated>
</template>
