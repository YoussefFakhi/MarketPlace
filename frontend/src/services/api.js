import axios from 'axios'

console.log('API Base URL:', import.meta.env.VITE_API_BASE_URL)

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000',
  withCredentials: false, // We're using Bearer tokens, not sessions
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

// Remove the CSRF interceptor since we're using token-based auth
// No interceptors needed for token-based auth

export default api