<script setup>
import { ref, computed, onMounted } from 'vue'
import { useMyServiceStore } from '@/stores/myService'
import { useCategoryStore } from '@/stores/category'
import {
  mdiViewList, mdiPlus, mdiPencil, mdiTrashCan, mdiCheckDecagram, mdiCloseOctagon
} from '@mdi/js'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionMain from '@/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import CardBox from '@/components/CardBox.vue'
import CardBoxModal from '@/components/CardBoxModal.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import BaseLevel from '@/components/BaseLevel.vue'
import PillTag from '@/components/PillTag.vue'
import FormField from '@/components/FormField.vue'
import FormControl from '@/components/FormControl.vue'
import NotificationBar from '@/components/NotificationBar.vue'

const myServiceStore = useMyServiceStore()
const categoryStore = useCategoryStore()

onMounted(() => {
  myServiceStore.fetchMyServices()
  categoryStore.fetchCategories()
})

const services = computed(() => myServiceStore.services)
const isLoading = computed(() => myServiceStore.loading)
const currentPage = computed(() => myServiceStore.pagination.current_page)
const numPages = computed(() => myServiceStore.pagination.last_page)
const pagesList = computed(() => {
  const list = []
  for (let i = 1; i <= numPages.value; i++) list.push(i)
  return list
})

// Modals
const isCreateModalActive = ref(false)
const isEditModalActive = ref(false)
const isDeleteModalActive = ref(false)
const currentService = ref(null)
const message = ref(null)

const createForm = ref({ title: '', description: '', price: '', category_id: '' })
const editForm = ref({ title: '', description: '', price: '', category_id: '' })

const openCreate = () => {
  createForm.value = { title: '', description: '', price: '', category_id: '' }
  isCreateModalActive.value = true
}

const openEdit = (service) => {
  currentService.value = service
  editForm.value = {
    title: service.title,
    description: service.description,
    price: service.price,
    category_id: service.category?.id || ''
  }
  isEditModalActive.value = true
}

const openDelete = (service) => {
  currentService.value = service
  isDeleteModalActive.value = true
}

const confirmCreate = async () => {
  try {
    await myServiceStore.createService(createForm.value)
    message.value = { type: 'success', text: 'Service created successfully!' }
  } catch (error) {
    message.value = { type: 'danger', text: typeof error === 'string' ? error : 'Failed to create service.' }
  }
}

const confirmEdit = async () => {
  if (!currentService.value) return
  try {
    await myServiceStore.updateService(currentService.value.id, editForm.value)
    message.value = { type: 'success', text: 'Service updated successfully!' }
  } catch (error) {
    message.value = { type: 'danger', text: typeof error === 'string' ? error : 'Failed to update.' }
  }
}

const confirmDelete = async () => {
  if (!currentService.value) return
  try {
    await myServiceStore.deleteService(currentService.value.id)
    message.value = { type: 'success', text: 'Service deleted.' }
  } catch (error) {
    message.value = { type: 'danger', text: error }
  }
}

const categories = computed(() => categoryStore.categories)
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiViewList" title="My Services" main>
        <BaseButton :icon="mdiPlus" color="info" label="New Service" @click="openCreate" />
      </SectionTitleLineWithButton>

      <NotificationBar v-if="message" :color="message.type" @dismiss="message = null">
        {{ message.text }}
      </NotificationBar>

      <!-- Create Modal -->
      <CardBoxModal v-model="isCreateModalActive" title="Create New Service" button="success" has-cancel @confirm="confirmCreate">
        <FormField label="Title"><FormControl v-model="createForm.title" placeholder="e.g. Professional Logo Design" /></FormField>
        <FormField label="Description"><FormControl v-model="createForm.description" type="textarea" placeholder="Describe your service..." /></FormField>
        <FormField label="Price ($)"><FormControl v-model="createForm.price" type="number" placeholder="49.99" /></FormField>
        <FormField label="Category">
          <FormControl v-model="createForm.category_id" type="select" :options="categories.map(c => ({ id: c.id, label: c.name }))" />
        </FormField>
      </CardBoxModal>

      <!-- Edit Modal -->
      <CardBoxModal v-model="isEditModalActive" title="Edit Service" button="info" has-cancel @confirm="confirmEdit">
        <FormField label="Title"><FormControl v-model="editForm.title" /></FormField>
        <FormField label="Description"><FormControl v-model="editForm.description" type="textarea" /></FormField>
        <FormField label="Price ($)"><FormControl v-model="editForm.price" type="number" /></FormField>
        <FormField label="Category">
          <FormControl v-model="editForm.category_id" type="select" :options="categories.map(c => ({ id: c.id, label: c.name }))" />
        </FormField>
      </CardBoxModal>

      <!-- Delete Modal -->
      <CardBoxModal v-model="isDeleteModalActive" title="Confirm Delete" button="danger" has-cancel @confirm="confirmDelete">
        <p>Are you sure you want to delete <b>{{ currentService?.title }}</b>?</p>
      </CardBoxModal>

      <CardBox has-table>
        <table :class="{ 'opacity-50 pointer-events-none': isLoading }">
          <thead>
            <tr>
              <th>Title</th>
              <th>Category</th>
              <th>Price</th>
              <th>Status</th>
              <th>Created</th>
              <th />
            </tr>
          </thead>
          <tbody>
            <tr v-for="service in services" :key="service.id">
              <td data-label="Title" class="font-medium">{{ service.title }}</td>
              <td data-label="Category">{{ service.category?.name || 'N/A' }}</td>
              <td data-label="Price" class="font-bold text-emerald-500">${{ service.price }}</td>
              <td data-label="Status">
                <PillTag :color="service.is_active ? 'success' : 'danger'" :label="service.is_active ? 'Active' : 'Inactive'" small />
              </td>
              <td data-label="Created">
                <small class="text-gray-500">{{ new Date(service.created_at).toLocaleDateString() }}</small>
              </td>
              <td class="before:hidden lg:w-1 whitespace-nowrap">
                <BaseButtons type="justify-start lg:justify-end" no-wrap>
                  <BaseButton color="info" :icon="mdiPencil" small @click="openEdit(service)" />
                  <BaseButton color="danger" :icon="mdiTrashCan" small @click="openDelete(service)" />
                </BaseButtons>
              </td>
            </tr>
            <tr v-if="services.length === 0 && !isLoading">
              <td colspan="6" class="p-8 text-center text-gray-400">You haven't created any services yet.</td>
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
                @click="myServiceStore.fetchMyServices(page)"
              />
            </BaseButtons>
            <small>Page {{ currentPage }} of {{ numPages }}</small>
          </BaseLevel>
        </div>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>
