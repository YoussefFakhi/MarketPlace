<script setup>
import { computed, onMounted, ref } from 'vue'
import { useServiceStore } from '@/stores/service'
import { mdiTrashCan, mdiEye, mdiCheckDecagram, mdiCloseOctagon } from '@mdi/js'
import CardBoxModal from '@/components/CardBoxModal.vue'
import BaseLevel from '@/components/BaseLevel.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import BaseButton from '@/components/BaseButton.vue'
import PillTag from '@/components/PillTag.vue'

const serviceStore = useServiceStore()

const isModalDangerActive = ref(false)
const isModalViewActive = ref(false)
const currentService = ref(null)

onMounted(() => {
  serviceStore.fetchServices()
})

const services = computed(() => serviceStore.services)
const isLoading = computed(() => serviceStore.loading)

const currentPage = computed(() => serviceStore.pagination.current_page)
const numPages = computed(() => serviceStore.pagination.last_page)

const pagesList = computed(() => {
  const pagesList = []
  for (let i = 1; i <= numPages.value; i++) {
    pagesList.push(i)
  }
  return pagesList
})

const deleteClick = (service) => {
  currentService.value = service
  isModalDangerActive.value = true
}

const viewClick = (service) => {
  currentService.value = service
  isModalViewActive.value = true
}

const toggleStatus = async (service) => {
  try {
    await serviceStore.toggleServiceStatus(service.id, service.is_active)
  } catch (error) {
    alert(error)
  }
}

const confirmDelete = async () => {
  if (currentService.value) {
    await serviceStore.deleteService(currentService.value.id)
  }
}
</script>

<template>
  <CardBoxModal
    v-model="isModalDangerActive"
    title="Please confirm"
    button="danger"
    has-cancel
    @confirm="confirmDelete"
  >
    <p>Are you sure you want to delete service <b>{{ currentService?.title }}</b>?</p>
    <p class="text-xs text-gray-500 mt-2">This action cannot be undone.</p>
  </CardBoxModal>

  <CardBoxModal
    v-model="isModalViewActive"
    title="Service Details"
    has-cancel
  >
    <div v-if="currentService" class="space-y-3">
      <p><b>Title:</b> {{ currentService.title }}</p>
      <p><b>Freelancer:</b> {{ currentService.user?.name }} ({{ currentService.user?.email }})</p>
      <p><b>Category:</b> {{ currentService.category?.name }}</p>
      <p><b>Price:</b> ${{ currentService.price }}</p>
      <p><b>Description:</b></p>
      <p class="text-sm bg-gray-100 p-3 rounded dark:bg-slate-700">{{ currentService.description }}</p>
      <div class="flex items-center justify-between border-t pt-2">
        <div class="flex items-center gap-2">
          <b>Status:</b>
          <PillTag :color="currentService.is_active ? 'success' : 'danger'" :label="currentService.is_active ? 'Active' : 'Inactive'" />
        </div>
        <BaseButton
          :color="currentService.is_active ? 'danger' : 'success'"
          :label="currentService.is_active ? 'Deactivate' : 'Activate'"
          :icon="currentService.is_active ? mdiCloseOctagon : mdiCheckDecagram"
          small
          @click="toggleStatus(currentService)"
        />
      </div>
    </div>
  </CardBoxModal>

  <table :class="{ 'opacity-50 pointer-events-none transition-opacity duration-300': isLoading }">
    <thead>
      <tr>
        <th>Freelancer</th>
        <th>Service Title</th>
        <th>Category</th>
        <th>Price</th>
        <th>Status</th>
        <th>Created</th>
        <th />
      </tr>
    </thead>
    <tbody>
      <tr v-for="service in services" :key="service.id">
        <td data-label="Freelancer">
          {{ service.user?.name || 'Unknown' }}
        </td>
        <td data-label="Title">
          {{ service.title }}
        </td>
        <td data-label="Category">
          {{ service.category?.name || 'N/A' }}
        </td>
        <td data-label="Price">
          ${{ service.price }}
        </td>
        <td data-label="Status">
          <PillTag :color="service.is_active ? 'success' : 'danger'" :label="service.is_active ? 'Active' : 'Inactive'" small />
        </td>
        <td data-label="Created" class="lg:w-1 whitespace-nowrap">
          <small class="text-gray-500 dark:text-slate-400">
            {{ new Date(service.created_at).toLocaleDateString() }}
          </small>
        </td>
        <td class="before:hidden lg:w-1 whitespace-nowrap">
          <BaseButtons type="justify-start lg:justify-end" no-wrap>
            <BaseButton
              :color="service.is_active ? 'warning' : 'success'"
              :icon="service.is_active ? mdiCloseOctagon : mdiCheckDecagram"
              small
              @click="toggleStatus(service)"
              :title="service.is_active ? 'Deactivate' : 'Activate'"
            />
            <BaseButton color="info" :icon="mdiEye" small @click="viewClick(service)" />
            <BaseButton color="danger" :icon="mdiTrashCan" small @click="deleteClick(service)" />
          </BaseButtons>
        </td>
      </tr>
    </tbody>
  </table>
  <div v-if="services.length === 0 && !isLoading" class="p-6 text-center text-gray-500">
    No services found in the marketplace.
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
          @click="serviceStore.fetchServices(page)"
        />
      </BaseButtons>
      <small>Page {{ currentPage }} of {{ numPages }}</small>
    </BaseLevel>
  </div>
</template>
