// authentification.ts
import api from './api'

interface LoginCredentials {
  email: string;
  password: string;
}

interface LoginResponse {
  token: string;
  user?: any;
}

export default {
  login(credentials: LoginCredentials) {
    return api.post<LoginResponse>('/login', credentials)
      .then(response => {
        // Handle token storage
        localStorage.setItem('token', response.data.token);
        return response;
      });
  },
  
  logout() {
    // First try to call API logout if token exists
    const token = localStorage.getItem('token');
    
    if (token) {
      // Try API logout but don't fail if it doesn't work
      return api.post('/logout').catch(error => {
        console.warn('API logout failed, but continuing with local logout:', error.message);
      }).finally(() => {
        // Always remove token regardless of API call result
        localStorage.removeItem('token');
      });
    } else {
      // No token, just resolve immediately
      localStorage.removeItem('token');
      return Promise.resolve();
    }
  },

  // Method to force logout without API call
  forceLogout() {
    localStorage.removeItem('token');
    return Promise.resolve();
  },

  // Check if user is authenticated
  isAuthenticated() {
    return !!localStorage.getItem('token');
  },

  // Get current token
  getToken() {
    return localStorage.getItem('token');
  }
}