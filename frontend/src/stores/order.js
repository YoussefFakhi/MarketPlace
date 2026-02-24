import { defineStore } from 'pinia';
import api from '../plugins/axios';

export const useOrderStore = defineStore('order', {
  state: () => ({
    orders: [],
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0
    }
  }),

  actions: {
    async fetchOrders(page = 1) {
      this.loading = true;
      try {
        const response = await api.get(`/orders?page=${page}`);
        this.orders = response.data.data;
        this.pagination = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          total: response.data.meta.total
        };
      } catch (error) {
        this.error = 'Failed to fetch orders';
        console.error(error);
      } finally {
        this.loading = false;
      }
    },

    async updateOrderStatus(id, status) {
      try {
        const response = await api.put(`/orders/${id}`, { status });
        const index = this.orders.findIndex(o => o.id === id);
        if (index !== -1) {
          this.orders[index] = response.data.data;
        }
        return response.data;
      } catch (error) {
        throw error.response?.data?.message || 'Failed to update order status';
      }
    }
  }
});
