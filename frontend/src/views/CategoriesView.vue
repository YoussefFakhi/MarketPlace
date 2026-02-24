<script setup>
import { ref } from 'vue'
import { mdiShapePlus, mdiFormatListBulletedType } from '@mdi/js'
import { useCategoryStore } from '@/stores/category'
import SectionMain from '@/components/SectionMain.vue'
import CardBox from '@/components/CardBox.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/components/BaseButton.vue'
import FormField from '@/components/FormField.vue'
import FormControl from '@/components/FormControl.vue'
import TableCategories from '@/components/TableCategories.vue'
import NotificationBar from '@/components/NotificationBar.vue'

const categoryStore = useCategoryStore()
const newCategoryName = ref('')    //This holds whatever the user types in the "Add Category" input field (starts empty)
const isLoading = ref(false)       //This tracks if we're waiting for the server to respond (true = spinning wheel, false = normal)
const message = ref(null)          // This shows success/error messages to the user (like "Added successfully!" or "Something went wrong")

//Add Category Function (The Action)
const addCategory = async () => {
  if (!newCategoryName.value) return   // If user didn't type anything, stop the function

  isLoading.value = true
  message.value = null
  try {
    await categoryStore.addCategory({ name: newCategoryName.value }) // Send the typed category name to our store
    newCategoryName.value = ''  // Clear the input field after successful addition
    message.value = { type: 'success', text: 'Category added successfully!' } // Show success message to user
  } catch (error) {
    message.value = { type: 'danger', text: error }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <LayoutAuthenticated>   <!-- Main page layout (header, sidebar, etc.) -->
    <SectionMain>    <!-- Main content area container -->
      <SectionTitleLineWithButton :icon="mdiFormatListBulletedType" title="Category Management" main> <!-- Title section at top -->
      </SectionTitleLineWithButton>

      <NotificationBar v-if="message" :color="message.type" :icon="mdiShapePlus" @dismiss="message = null">
        {{ message.text }}
      </NotificationBar>

      <CardBox class="mb-6">
        <form @submit.prevent="addCategory">
          <FormField label="Add New Category" help="Type a name and press enter to add a new category">
            <div class="flex gap-2">
              <FormControl
                v-model="newCategoryName"
                placeholder="Category name (e.g. Graphic Design)"
                :disabled="isLoading"
              />
              <BaseButton
                type="submit"
                color="info"
                label="Add"
                :icon="mdiShapePlus"
                :disabled="!newCategoryName || isLoading"
              />
            </div>
          </FormField>
        </form>
      </CardBox>

      <CardBox has-table>
        <TableCategories />
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>
