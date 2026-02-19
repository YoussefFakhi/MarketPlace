import { defineStore } from 'pinia';
import api from '../plugins/axios';

export const useUserStore = defineStore('user', {
  state: () => ({
    users: [],
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0
    }
  }),

  actions: {
    async fetchUsers(page = 1) {
      this.loading = true;
      try {
        const response = await api.get(`/users?page=${page}`);
        this.users = response.data.data;
        this.pagination = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          total: response.data.meta.total
        };
      } catch (error) {
        this.error = 'Failed to fetch users';
        console.error(error);
      } finally {
        this.loading = false;
      }
    },

    async deleteUser(id) {
      try {
        await api.delete(`/users/${id}`);
        this.users = this.users.filter(u => u.id !== id);
      } catch (error) {
        throw error.response?.data?.message || 'Failed to delete user';
      }
    }
  }
});
