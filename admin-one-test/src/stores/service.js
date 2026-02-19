import { defineStore } from 'pinia';
import api from '../plugins/axios';

export const useServiceStore = defineStore('service', {
  state: () => ({
    services: [],
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0
    }
  }),

  actions: {
    async fetchServices(page = 1) {
      this.loading = true;
      try {
        const response = await api.get(`/services?page=${page}`);
        // Laravel's paginate returns data in response.data.data
        this.services = response.data.data;
        this.pagination = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          total: response.data.meta.total
        };
      } catch (error) {
        this.error = 'Failed to fetch services';
        console.error(error);
      } finally {
        this.loading = false;
      }
    },

    async toggleServiceStatus(id, isActive) {
      try {
        // !isActive flips 'true' to 'false' (or vice versa)
        // We reuse the update endpoint since the Policy allows admins to update
        const response = await api.put(`/services/${id}`, { is_active: !isActive });
        // This part updates the UI locally so you see the change immediately
        const index = this.services.findIndex(s => s.id === id);
        if (index !== -1) {
          this.services[index].is_active = !isActive;
        }
        return response.data;
      } catch (error) {
        throw error.response?.data?.message || 'Failed to update service status';
      }
    },

    async deleteService(id) {
      try {
        await api.delete(`/services/${id}`);
        this.services = this.services.filter(s => s.id !== id);
      } catch (error) {
        throw error.response?.data?.message || 'Failed to delete service';
      }
    }
  }
});
