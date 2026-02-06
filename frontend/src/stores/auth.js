import { defineStore } from 'pinia'
import api from '../services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: false,
    error: null,
    token: null  // Add token storage
  }),

  getters: {
    isLoggedIn: (state) => !!state.user,
    role: (state) => state.user?.role || null,
    userName: (state) => state.user?.name || '',
    userEmail: (state) => state.user?.email || ''
  },

  actions: {
    // Fetch current user information
    async fetchUser() {
      if (!this.token) {
        this.user = null
        return
      }
      
      this.loading = true
      this.error = null
      
      try {
        // Add token to headers for this request
        const response = await api.get('/api/user', {
          headers: {
            'Authorization': `Bearer ${this.token}`
          }
        })
        this.user = response.data
      } catch (error) {
        this.user = null
        this.token = null
        if (error.response?.status !== 401) {
          this.error = error.message
        }
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await api.post('/api/register', userData)
        
        // Store both token and user
        this.token = response.data.token
        this.user = response.data.user
        
        // Set token in axios defaults for future requests
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async login(credentials) {
      this.loading = true
      this.error = null
      
      try {
        const response = await api.post('/api/login', credentials)
        
        // Store both token and user
        this.token = response.data.token
        this.user = response.data.user
        
        // Set token in axios defaults for future requests
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true
      this.error = null
      
      try {
        await api.post('/api/logout', {}, {
          headers: {
            'Authorization': `Bearer ${this.token}`
          }
        })
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        // Clear both token and user
        this.token = null
        this.user = null
        delete api.defaults.headers.common['Authorization']
        this.loading = false
      }
    }
  }
})
