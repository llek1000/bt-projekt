import api from './api'

interface LoginCredentials {
  email: string;
  password: string;
}

interface LoginResponse {
  token: string;
  user?: any;
  redirect_url?: string;
}

export default {
  login(credentials: LoginCredentials) {
    return api.post<LoginResponse>('/login', credentials)
      .then(response => {
        localStorage.setItem('token', response.data.token);
        
        const redirectUrl = localStorage.getItem('redirectAfterLogin')
        if (redirectUrl) {
          localStorage.removeItem('redirectAfterLogin')
          setTimeout(() => {
            window.location.href = redirectUrl
          }, 100)
        }
        
        return response;
      });
  },
  
  logout() {
    const token = localStorage.getItem('token');
    
    if (token) {
      return api.post('/logout').catch(error => {
        console.warn('API logout failed, but continuing with local logout:', error.message);
      }).finally(() => {
        localStorage.removeItem('token');
        localStorage.removeItem('redirectAfterLogin');
      });
    } else {
      localStorage.removeItem('token');
      localStorage.removeItem('redirectAfterLogin');
      return Promise.resolve();
    }
  },

  forceLogout() {
    localStorage.removeItem('token');
    localStorage.removeItem('redirectAfterLogin');
    return Promise.resolve();
  },

  isAuthenticated() {
    return !!localStorage.getItem('token');
  },

  getToken() {
    return localStorage.getItem('token');
  },

  async getCurrentUser() {
    try {
      if (!this.isAuthenticated()) {
        return null;
      }
      
      const response = await api.get('/user');
      return response.data.user;
    } catch (error) {
      console.error('Error getting current user:', error);
      return null;
    }
  },

  async hasRole(roleName: string) {
    try {
      const user = await this.getCurrentUser();
      if (!user || !user.roles) return false;
      
      return user.roles.some((role: any) => 
        role.name.toLowerCase() === roleName.toLowerCase()
      );
    } catch (error) {
      console.error('Error checking user role:', error);
      return false;
    }
  },

  async getRedirectUrl() {
    try {
      if (await this.hasRole('admin')) return '/admin/dashboard';
      if (await this.hasRole('editor')) return '/edit/dashboard';
      return '/';
    } catch (error) {
      console.error('Error getting redirect URL:', error);
      return '/';
    }
  }
}