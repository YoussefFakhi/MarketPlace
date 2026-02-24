import { defineStore } from 'pinia';
import api from '../plugins/axios';

export const useStatsStore = defineStore('stats', {
  state: () => ({
    totals: {
      revenue: 0,
      orders: 0,
      services: 0,
      users: 0
    },
    charts: {
      labels: [],
      orders: [],
      revenue: []
    },
    statusDistribution: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchStats() {
      this.loading = true;
      try {
        const response = await api.get('/stats');
        const data = response.data.data;
        
        this.totals = data.totals;
        this.charts = data.charts;
        this.statusDistribution = data.status_distribution;
      } catch (error) {
        this.error = 'Failed to fetch global stats';
        console.error(error);
      } finally {
        this.loading = false;
      }
    }
  }
});
