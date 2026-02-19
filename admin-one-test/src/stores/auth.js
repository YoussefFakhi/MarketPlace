import { defineStore } from 'pinia';
import api from '../plugins/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('admin_user')) || null,
    token: localStorage.getItem('admin_token') || null,
  }),

  getters: { // just can take the vaallue from the state
    isAuthenticated: (state) => !!state.token,
    role: (state) => state.user?.role || null,
    isAdmin: (state) => state.user?.role === 'admin',
  },

  actions: { // action can modifie the value inside the state
    async login(credentials) {
      try {
        // Backend expects email and password
        const response = await api.post('/login', credentials);

        // Check if the user is actually an admin before proceding
        if (response.data.user.role !== 'admin') {
          throw new Error('Access denied. Admin privileges required.');
        }

        this.token = response.data.token;
        this.user = response.data.user;

        localStorage.setItem('admin_token', this.token);
        localStorage.setItem('admin_user', JSON.stringify(this.user));

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
      localStorage.removeItem('admin_token');
      localStorage.removeItem('admin_user');
    },

    async fetchUser() {
      try {
        const response = await api.get('/user');
        if (response.data.role !== 'admin') {
          this.logout();
          return;
        }
        this.user = response.data;
        localStorage.setItem('admin_user', JSON.stringify(response.data));
      } catch (error) {
        this.logout();
        throw error;
      }
    },

    async updateProfile(data) {
      try {
        const response = await api.put('/profile', data);
        this.user = response.data.user;
        localStorage.setItem('admin_user', JSON.stringify(this.user));
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
