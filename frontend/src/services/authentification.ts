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
    localStorage.removeItem('token');
    return api.post('/logout');
  }
}