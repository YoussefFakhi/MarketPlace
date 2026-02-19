import { defineStore } from 'pinia';
import api from '../plugins/axios';

export const useAuthStore = defineStore('auth',{
    state:()=>({
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('token') || null,
    }),
    getters: { // just can take the vaallue from the state 
        isAuthenticated: (state) => !!state.token,
        role: (state) => state.user?.role || null,
        isClient: (state) => state.user?.role === 'client',
        isFreelancer: (state) => state.user?.role === 'freelancer',
        isAdmin: (state) => state.user?.role === 'admin',
    },

    actions:{// action can modifie the value inside the state 
        async login(credentials){
            try {
                const response = api.get('/login',credentials);
                this.token = response.data.token;
                this.user = response.data.user;

                localStorage.setItem('token',this.token);
                localStorage.setItem('user',JSON.stringify(this.user));

                return true;
            } catch (error) {
                throw error.response?.data?.message || 'Login failed';
            }
        },

        async register(userData){
            try {
                const response = await api.post('/register', userData);
                this.token = response.data.token;
                this.user = response.data.user;

                localStorage.setItem('token', this.token);
                localStorage.setItem('user', JSON.stringify(this.user));

                return true;
            } catch (error) {
                throw error.response?.data?.message || 'Registration failed';
            }
        },
        async logout() {
            try {
                // 1. Tell Laravel to destroy the token
                await api.post('/logout');
            } catch (error) {
                // 2. If Laravel fails, we can still logout locally
                console.error(' backend Logout failed:', error);
            } finally{
                this.token = null;
                this.user = null;
                localStorage.removeItem('token');
                localStorage.removeItem('user');
            }
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
        }
    }
})