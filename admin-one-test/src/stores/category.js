import { defineStore } from 'pinia';
import api from '../plugins/axios';

export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [],
    loading: false,
    error: null,
    pagination:{
      current_page:1,
      last_page:1,
      total:0
    }
  }),

  actions: {
    async fetchCategories(page=1) {
      this.loading = true;
      try {
        const response = await api.get(`/categories?page=${page}`);
        this.categories = response.data.data;
        this.pagination = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          total: response.data.meta.total
        }
      } catch (error) {
        this.error = 'Failed to fetch categories';
        console.error(error);
      } finally {
        this.loading = false;
      }
    },

    async addCategory(data) {
      try {
        const response = await api.post('/categories', data);
        await this.fetchCategories(this.pagination.current_page); // Refresh current page
        return response.data;
      } catch (error) {
        throw error.response?.data?.message || 'Failed to add category';
      }
    },

    async updateCategory(id, data) {
      try {
        const response = await api.put(`/categories/${id}`, data);
        const index = this.categories.findIndex(c => c.id === id);
        if (index !== -1) {
          this.categories[index] = response.data.data;
        }
        return response.data;
      } catch (error) {
        throw error.response?.data?.message || 'Failed to update category';
      }
    },

    async deleteCategory(id) {
      try {
        await api.delete(`/categories/${id}`);
        await this.fetchCategories(this.pagination.current_page); // Refresh current page
      } catch (error) {
        throw error.response?.data?.message || 'Failed to delete category';
      }
    }
  }
});
