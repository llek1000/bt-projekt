import api from './api'

interface UserData {
  username: string;
  email: string;
  password?: string;
  password_confirmation?: string;
  roles?: string[];  
}

interface RoleData {
  id: number;
  name: string;
  permissions: string[];
}

interface QueryParams {
  page?: number;
  limit?: number;
  sort?: string;
  order?: 'asc' | 'desc';
  search?: string;
  status?: string;
  department?: string;
}

interface ConferenceYearData {
  year: string;
  semester: 'Winter' | 'Summer';
  is_active?: boolean;
}

export default {
  // User Management (Admin only)
  getUsers(params?: QueryParams) {
    return api.get<{ users: UserData[] }>('/admin/users', { params });
  },

  createUser(userData: UserData) {
    return api.post<{ user: UserData }>('/admin/users', userData);
  },

  updateUser(id: number, userData: Partial<UserData>) {
    return api.put<{ user: UserData }>(`/admin/users/${id}`, userData);
  },

  deleteUser(id: number) {
    return api.delete(`/admin/users/${id}`);
  },

  // Role Management (Admin only)
  getRoles(params?: QueryParams) {
    return api.get<{ roles: RoleData[] }>('/admin/roles', { params });
  },

  assignRole(userId: number, roleId: number) {
    return api.post(`/admin/users/${userId}/roles`, { roleId });
  },

  // Conference Years Management (Admin only)
  getConferenceYears() { 
    return api.get<{ data: any[] }>('/conference-years') 
  },

  createYear(data: ConferenceYearData) { 
    return api.post<{ data: any }>('/admin/conference-years', data) 
  },

  updateYear(id: number, data: Partial<ConferenceYearData>) { 
    return api.put<{ data: any }>(`/admin/conference-years/${id}`, data) 
  },

  deleteYear(id: number) { 
    return api.delete(`/admin/conference-years/${id}`) 
  },

  // Editor Assignment Management (Admin only)
  listYearEditors(yearId: number) { 
    return api.get<{ data: any[] }>(`/admin/years/${yearId}/editors`) 
  },
  
  assignEditor(yearId: number, userId: number) { 
    return api.post<{ data: any }>(`/admin/years/${yearId}/editors`, { user_id: userId }) 
  },
  
  removeEditor(yearId: number, assignmentId: number) { 
    return api.delete(`/admin/years/${yearId}/editors/${assignmentId}`) 
  },

  // Article Management (Admin can manage all articles)
  listArticles(params?: { conference_year_id?: number; search?: string }) {
    return api.get<{ data: any[] }>('/articles', { params })
  },

  createArticle(data: any) {
    return api.post<{ data: any }>('/editor/articles', data)
  },

  updateArticle(id: number, data: any) {
    return api.put<{ data: any }>(`/editor/articles/${id}`, data)
  },

  deleteArticle(id: number) {
    return api.delete(`/editor/articles/${id}`)
  },

  bulkDeleteArticles(articleIds: number[]) {
    return api.post('/editor/articles/bulk-delete', { article_ids: articleIds })
  },

  // Subpages Management (Admin only)
  listSubpages(yearId: number) { 
    return api.get<{ data: any[] }>(`/admin/years/${yearId}/subpages`) 
  },
  
  createSubpage(yearId: number, data: any) { 
    return api.post<{ data: any }>(`/admin/years/${yearId}/subpages`, data) 
  },
  
  updateSubpage(id: number, data: any) { 
    return api.put<{ data: any }>(`/admin/subpages/${id}`, data) 
  },
  
  deleteSubpage(id: number) { 
    return api.delete(`/admin/subpages/${id}`) 
  },

  // File Upload (Available for admins)
  uploadImage(formData: FormData) {
    return api.post<{ location: string; filename: string; path: string }>('/upload-image', formData, {
      headers: { 
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    });
  },

  // Statistics and Reports (Admin only)
  getUserStatistics() {
    return api.get<{ data: any }>('/admin/statistics/users')
  },

  getArticleStatistics() {
    return api.get<{ data: any }>('/admin/statistics/articles')
  },

  getConferenceYearStatistics(yearId: number) {
    return api.get<{ data: any }>(`/conference-years/${yearId}/statistics`)
  },

  // System Management (Admin only)
  getSystemInfo() {
    return api.get<{ data: any }>('/admin/system/info')
  },

  // Helper methods for admins
  helpers: {
    // Format user role for display
    formatUserRole(user: any): string {
      if (!user.roles || user.roles.length === 0) return 'No Role'
      return user.roles.map((role: any) => role.name).join(', ')
    },

    // Get role badge class
    getRoleBadgeClass(roleName: string): string {
      switch (roleName?.toLowerCase()) {
        case 'admin': return 'badge-admin'
        case 'editor': return 'badge-editor'
        default: return 'badge-default'
      }
    },

    // Format date for admin interface
    formatDate(dateString?: string): string {
      if (!dateString) return 'Unknown date'
      
      try {
        return new Date(dateString).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        })
      } catch {
        return 'Invalid date'
      }
    },

    // Validate user data
    validateUser(user: UserData): string[] {
      const errors: string[] = []

      if (!user.username?.trim()) {
        errors.push('Username is required')
      }

      if (!user.email?.trim()) {
        errors.push('Email is required')
      }

      if (user.email && !/\S+@\S+\.\S+/.test(user.email)) {
        errors.push('Email format is invalid')
      }

      if (user.password && user.password.length < 8) {
        errors.push('Password must be at least 8 characters')
      }

      if (user.password && user.password !== user.password_confirmation) {
        errors.push('Password confirmation does not match')
      }

      return errors
    },

    // Validate conference year data
    validateConferenceYear(year: ConferenceYearData): string[] {
      const errors: string[] = []

      if (!year.year?.trim()) {
        errors.push('Year is required')
      }

      if (year.year && !/^\d{4}$/.test(year.year)) {
        errors.push('Year must be a 4-digit number')
      }

      if (!year.semester) {
        errors.push('Semester is required')
      }

      if (year.semester && !['Winter', 'Summer'].includes(year.semester)) {
        errors.push('Semester must be either Winter or Summer')
      }

      return errors
    }
  }
}