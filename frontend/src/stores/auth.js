import { defineStore } from 'pinia';
import api from '../plugins/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    role: (state) => state.user?.role || null,
    isAdmin: (state) => state.user?.role === 'admin',
    isFreelancer: (state) => state.user?.role === 'freelancer',
    isClient: (state) => state.user?.role === 'client',
  },

  actions: {
    async login(credentials) {
      try {
        const response = await api.post('/login', credentials);

        this.token = response.data.token;
        this.user = response.data.user;

        localStorage.setItem('token', this.token);
        localStorage.setItem('user', JSON.stringify(this.user));

        return true;
      } catch (error) {
        if (error.response) {
          throw error.response.data.message || 'Login failed';
        }
        throw error.message || 'Login failed';
      }
    },

    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      // Also clear old admin keys if present
      localStorage.removeItem('admin_token');
      localStorage.removeItem('admin_user');
    },

    async fetchUser() {
      try {
        const response = await api.get('/user');
        this.user = response.data;
        localStorage.setItem('user', JSON.stringify(response.data));
      } catch (error) {
        this.logout();
        throw error;
      }
    },

    async updateProfile(data) {
      try {
        const response = await api.put('/profile', data);
        this.user = response.data.user;
        localStorage.setItem('user', JSON.stringify(this.user));
        return response.data;
      } catch (error) {
        throw error.response?.data?.message || 'Failed to update profile';
      }
    },

    async updatePassword(data) {
      try {
        const response = await api.put('/profile/password', data);
        return response.data;
      } catch (error) {
        throw error.response?.data?.message || 'Failed to update password';
      }
    }
  }
});
