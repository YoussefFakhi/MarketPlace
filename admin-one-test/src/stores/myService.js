import { defineStore } from 'pinia';
import api from '../plugins/axios';

// Freelancer's own services store - uses /myservices endpoint
export const useMyServiceStore = defineStore('myService', {
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
    async fetchMyServices(page = 1) {
      this.loading = true;
      try {
        const response = await api.get(`/myservices?page=${page}`);
        this.services = response.data.data;
        this.pagination = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          total: response.data.meta.total
        };
      } catch (error) {
        this.error = 'Failed to fetch your services';
        console.error(error);
      } finally {
        this.loading = false;
      }
    },

    async createService(data) {
      try {
        const response = await api.post('/services', data);
        await this.fetchMyServices(this.pagination.current_page);
        return response.data;
      } catch (error) {
        throw error.response?.data?.errors || error.response?.data?.message || 'Failed to create service';
      }
    },

    async updateService(id, data) {
      try {
        const response = await api.put(`/services/${id}`, data);
        const index = this.services.findIndex(s => s.id === id);
        if (index !== -1) {
          this.services[index] = response.data.data;
        }
        return response.data;
      } catch (error) {
        throw error.response?.data?.errors || error.response?.data?.message || 'Failed to update service';
      }
    },

    async deleteService(id) {
      try {
        await api.delete(`/services/${id}`);
        await this.fetchMyServices(this.pagination.current_page);
      } catch (error) {
        throw error.response?.data?.message || 'Failed to delete service';
      }
    }
  }
});
