<script setup>
    import { computed, ref, onMounted } from 'vue'; //
    import { useCategoryStore } from '@/stores/category'; //
    import { mdiTrashCan, mdiPencil} from '@mdi/js'   //mdiEye   mdiPlus
    import CardBoxModal from '@/components/CardBoxModal.vue'
    //import BaseLevel from '@/components/BaseLevel.vue'
    import BaseButtons from '@/components/BaseButtons.vue'
    import BaseButton from '@/components/BaseButton.vue'
   // import UserAvatar from '@/components/UserAvatar.vue'
    import FormField from '@/components/FormField.vue'
    import FormControl from '@/components/FormControl.vue'
import BaseLevel from '@/components/BaseLevel.vue';

    const categoryStore = useCategoryStore();

    const isModalDangerActive = ref(false)   // Delete confirmation modal
    const isModalEditActive = ref(false)     // Edit modal
    const currentCategory = ref(null)        // Tracks which category is being edited/deleted
    const editForm = ref({ name: '' })       // Edit form data

    onMounted(()=>{
        categoryStore.fetchCategories()
    })

    const categories = computed(()=>categoryStore.categories);
    const isLoading = computed(() => categoryStore.loading)

    const currentPage = computed(() => categoryStore.pagination.current_page)
    const numPages = computed(() => categoryStore.pagination.last_page)

    const pagesList = computed(() => {
      const pagesList = []
      for (let i = 1; i <= numPages.value; i++) {
        pagesList.push(i)
      }
      return pagesList
    })



    const editClick = (category) => {
        currentCategory.value= category
        editForm.value.name = category.name
        isModalEditActive.value=true
    }

    const deleteClick = (category)=>{
      currentCategory.value = category
      isModalDangerActive.value = true    // we make .value becose we can t access to a ref() its like locked out without .value we cant access it
    }

    const confirmDelete = async ()=>{
       if(currentCategory.value){
        await categoryStore.deleteCategory(currentCategory.value.id)
       }
    }

    const confirmEdit = async ()=>{
      if(currentCategory.value){
        await categoryStore.updateCategory(currentCategory.value.id,editForm.value)
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
    <p>Are you sure you want to delete category <b>{{ currentCategory?.name }}</b>?</p>
  </CardBoxModal>

  <CardBoxModal
    v-model="isModalEditActive"
    title="Edit Category"
    button="info"
    has-cancel
    @confirm="confirmEdit"
  >
    <FormField label="Category Name">
      <FormControl v-model="editForm.name" placeholder="Enter category name" />
    </FormField>
  </CardBoxModal>

  <table :class="{ 'opacity-50 pointer-events-none transition-opacity duration-300': isLoading }">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Slug</th>
        <th>Created</th>
        <th />
      </tr>
    </thead>
    <tbody>
      <tr v-for="category in categories" :key="category.id">
        <td data-label="ID">
          {{ category.id }}
        </td>
        <td data-label="Name">
          {{ category.name }}
        </td>
        <td data-label="Slug">
          {{ category.slug }}
        </td>
        <td data-label="Created" class="lg:w-1 whitespace-nowrap">
          <small class="text-gray-500 dark:text-slate-400" :title="category.created_at">
            {{ new Date(category.created_at).toLocaleDateString() }}<!-- change data structure -->
          </small>

        </td>
        <td class="before:hidden lg:w-1 whitespace-nowrap">
          <BaseButtons type="justify-start lg:justify-end" no-wrap>
            <BaseButton color="info" :icon="mdiPencil" small @click="editClick(category)" />
            <BaseButton color="danger" :icon="mdiTrashCan" small @click="deleteClick(category)" />
          </BaseButtons>
        </td>
      </tr>
    </tbody>
  </table>
  <div v-if="categories.length === 0 && !isLoading" class="p-6 text-center text-gray-500">
    No categories found.
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
          @click="categoryStore.fetchCategories(page)"
        />
      </BaseButtons>
      <small>Page {{ currentPage }} of {{ numPages }}</small>
    </BaseLevel>
  </div>
</template>
