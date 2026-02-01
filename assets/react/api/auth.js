import apiClient from './client';
import { STORAGE_KEYS } from '../utils/constants';

export const authApi = {
  /**
   * Login user
   */
  async login(email, password) {
    const response = await apiClient.post('/auth/login', { email, password });
    const { token, refresh_token } = response.data;
    
    // Store tokens
    localStorage.setItem(STORAGE_KEYS.TOKEN, token);
    if (refresh_token) {
      localStorage.setItem(STORAGE_KEYS.REFRESH_TOKEN, refresh_token);
    }
    
    // Fetch user profile
    const userResponse = await this.getProfile();
    return userResponse;
  },
  
  /**
   * Register new user
   */
  async register(data) {
    const response = await apiClient.post('/auth/register', {
      email: data.email,
      password: data.password,
      name: data.name,
      phone: data.phone || null,
      countryId: data.countryId,
    });
    return response.data;
  },
  
  /**
   * Get current user profile
   */
  async getProfile() {
    const response = await apiClient.get('/auth/me');
    const user = response.data;
    localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(user));
    return user;
  },
  
  /**
   * Logout user
   */
  logout() {
    localStorage.removeItem(STORAGE_KEYS.TOKEN);
    localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
    localStorage.removeItem(STORAGE_KEYS.USER);
  },
  
  /**
   * Check if user is authenticated
   */
  isAuthenticated() {
    return !!localStorage.getItem(STORAGE_KEYS.TOKEN);
  },
  
  /**
   * Get stored user
   */
  getStoredUser() {
    const user = localStorage.getItem(STORAGE_KEYS.USER);
    return user ? JSON.parse(user) : null;
  },
};
