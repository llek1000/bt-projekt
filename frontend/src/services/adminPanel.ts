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

export default {
  // User Management
  getUsers(params?: QueryParams): Promise<UserData[]> {
    return api.get('/admin/users', { params });
  },

  createUser(userData: UserData): Promise<UserData> {
    return api.post('/admin/users', userData);
  },

  updateUser(id: number, userData: Partial<UserData>): Promise<UserData> {
    return api.put(`/admin/users/${id}`, userData);
  },

  deleteUser(id: number): Promise<void> {
    return api.delete(`/admin/users/${id}`);
  },

  // Role Management
  getRoles(params?: QueryParams): Promise<RoleData[]> {
    return api.get('/admin/roles', { params });
  },

  assignRole(userId: number, roleId: number): Promise<void> {
    return api.post(`/admin/users/${userId}/roles`, { roleId });
  },

  // PUBLIC Conference‐years (reads only)
  getConferenceYears() { 
    return api.get<{ data: any[] }>('/conference-years') 
  },

  // Conference‐years CRUD (admin)
  createYear(data: any)     { return api.post('/admin/conference-years', data) },
  updateYear(id: number, data: any) { return api.put(`/admin/conference-years/${id}`, data) },
  deleteYear(id: number)       { return api.delete(`/admin/conference-years/${id}`) },

  // Editor assignments
  listYearEditors(yearId: number){ return api.get(`/admin/years/${yearId}/editors`) },
  assignEditor(yearId: number, userId: number){ return api.post(`/admin/years/${yearId}/editors`,{user_id:userId}) },
  removeEditor(yearId: number, assignmentId: number){ return api.delete(`/admin/years/${yearId}/editors/${assignmentId}`) },

  // Subpages
  listSubpages(yearId: number){ return api.get(`/admin/years/${yearId}/subpages`) },
  createSubpage(yearId: number, data: any){ return api.post(`/admin/years/${yearId}/subpages`,data) },
  updateSubpage(id: number, data: any){ return api.put(`/admin/subpages/${id}`,data) },
  deleteSubpage(id: number){ return api.delete(`/admin/subpages/${id}`) },

  // Articles
  listArticles(params?: { conference_year_id?: number }) {
    return api.get<{ data: any[] }>('/articles', { params })
  },
  createArticle(data: any) {
    return api.post('/editor/articles', data)
  },
  updateArticle(id: number, data: any) {
    return api.put(`/editor/articles/${id}`, data)
  },
  deleteArticle(id: number) {
    return api.delete(`/editor/articles/${id}`)
  },
}