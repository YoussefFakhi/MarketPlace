import axios from 'axios';
import { useAuthStore } from '../stores/auth';

// Create axios instance  automatically uses base URL and headers
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  timeout: 10000,//Sets a 10-second timeout for requests Prevents users waiting
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Request interceptor - add token to all requests
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore();
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - handle 401 auto logout
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // This runs when HTTP errors occur (400, 401, 404, 500, etc.)
    if (error.response?.status === 401) {
    // If server says "Unauthorized" (401)
      const authStore = useAuthStore();
      authStore.logout();  // Clear user session
      window.location.href = '/login';  // Redirect to login
    }
    return Promise.reject(error);  // Pass error to component
  }
);

export default api;
