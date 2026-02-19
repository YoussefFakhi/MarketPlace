<script setup>
import { computed, ref, onMounted } from 'vue'
import { useUserStore } from '@/stores/user'
import { mdiTrashCan, mdiAccountEdit, mdiAccountCircle } from '@mdi/js'
import CardBoxModal from '@/components/CardBoxModal.vue'
import BaseLevel from '@/components/BaseLevel.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import BaseButton from '@/components/BaseButton.vue'
import UserAvatar from '@/components/UserAvatar.vue'
import PillTag from '@/components/PillTag.vue'

const userStore = useUserStore()

const isModalDangerActive = ref(false)
const currentUser = ref(null)

onMounted(() => {
  userStore.fetchUsers()
})

const users = computed(() => userStore.users)
const isLoading = computed(() => userStore.loading)

const currentPage = computed(() => userStore.pagination.current_page)
const numPages = computed(() => userStore.pagination.last_page)

const pagesList = computed(() => {
  const pagesList = []
  for (let i = 1; i <= numPages.value; i++) {
    pagesList.push(i)
  }
  return pagesList
})

const deleteClick = (user) => {
  currentUser.value = user
  isModalDangerActive.value = true
}

const confirmDelete = async () => {
  if (currentUser.value) {
    try {
      await userStore.deleteUser(currentUser.value.id)
    } catch (error) {
      alert(error)
    }
  }
}

const getRoleColor = (role) => {
  switch (role) {
    case 'admin': return 'danger'
    case 'freelancer': return 'info'
    case 'client': return 'success'
    default: return 'info'
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
    <p>Are you sure you want to delete user <b>{{ currentUser?.name }}</b>?</p>
    <p class="text-xs text-gray-500 mt-2">This will permanently remove their account and all associated data.</p>
  </CardBoxModal>

  <table :class="{ 'opacity-50 pointer-events-none transition-opacity duration-300': isLoading }">
    <thead>
      <tr>
        <th />
        <th>Name</th>
        <th>E-mail</th>
        <th>Role</th>
        <th>Created</th>
        <th />
      </tr>
    </thead>
    <tbody>
      <tr v-for="user in users" :key="user.id">
        <td class="border-b-0 lg:w-6 before:hidden">
          <UserAvatar :username="user.name" class="w-24 h-24 mx-auto lg:w-6 lg:h-6" />
        </td>
        <td data-label="Name">
          {{ user.name }}
        </td>
        <td data-label="E-mail">
          {{ user.email }}
        </td>
        <td data-label="Role">
          <PillTag :color="getRoleColor(user.role)" :label="user.role" small />
        </td>
        <td data-label="Created" class="lg:w-1 whitespace-nowrap">
          <small class="text-gray-500 dark:text-slate-400">
            {{ new Date(user.created_at).toLocaleDateString() }}
          </small>
        </td>
        <td class="before:hidden lg:w-1 whitespace-nowrap">
          <BaseButtons type="justify-start lg:justify-end" no-wrap>
            <BaseButton
              color="danger"
              :icon="mdiTrashCan"
              small
              @click="deleteClick(user)"
              :disabled="user.role === 'admin'"
            />
          </BaseButtons>
        </td>
      </tr>
    </tbody>
  </table>
  <div v-if="users.length === 0 && !isLoading" class="p-6 text-center text-gray-500">
    No users found in the system.
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
          @click="userStore.fetchUsers(page)"
        />
      </BaseButtons>
      <small>Page {{ currentPage }} of {{ numPages }}</small>
    </BaseLevel>
  </div>
</template>
