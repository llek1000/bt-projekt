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

interface FileData {
  id: number;
  name: string;
  original_name: string;
  file_path: string;
  mime_type: string;
  file_size: number;
  file_size_human: string;
  download_url: string;
  category?: string;
  article_id?: number;
  uploaded_by: number;
  created_at: string;
  updated_at: string;
}

interface AssignmentData {
  user_id: number;
  conference_year_id: number;
}

export default {
  // User Management (Admin only)
  getUsers(params?: QueryParams) {
    return api.get('/admin/users', { params })
  },

  createUser(userData: UserData) {
    return api.post('/admin/users', userData)
  },

  updateUser(id: number, userData: Partial<UserData>) {
    return api.put(`/admin/users/${id}`, userData)
  },

  deleteUser(id: number) {
    return api.delete(`/admin/users/${id}`)
  },

  // Role Management (Admin only)
  getRoles(params?: QueryParams) {
    return api.get('/admin/roles', { params })
  },

  assignRole(userId: number, roleId: number) {
    return api.post(`/admin/users/${userId}/assign-role`, { roleId })
  },

  // Conference Years Management (Admin only)
  getConferenceYears() {
    return api.get('/admin/conference-years')
  },

  createYear(data: ConferenceYearData) {
    return api.post('/admin/conference-years', data)
  },

  updateYear(id: number, data: Partial<ConferenceYearData>) {
    return api.put(`/admin/conference-years/${id}`, data)
  },

  deleteYear(id: number) {
    return api.delete(`/admin/conference-years/${id}`)
  },

  // Editor Assignment Management (Admin only)
  listYearEditors(yearId: number) {
    return api.get(`/admin/years/${yearId}/editors`)
  },
  
  assignEditor(yearId: number, userId: number) {
    return api.post(`/admin/years/${yearId}/editors`, { user_id: userId })
  },
  
  removeEditor(yearId: number, assignmentId: number) {
    return api.delete(`/admin/years/${yearId}/editors/${assignmentId}`)
  },

  // Get all users with editor role for assignments
  getEditors() {
    return api.get('/admin/editors')
  },

  // Get conference years with their assigned editors - PRIDANÁ METÓDA
  getYearsWithEditors() {
    return api.get('/admin/years-with-editors')
  },

  // Get all assignments
  getAllAssignments() {
    return api.get('/admin/assignments')
  },

  // Article Management (Admin can manage all articles)
  listArticles(params?: { conference_year_id?: number; search?: string }) {
    return api.get('/articles', { params })
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

  bulkDeleteArticles(articleIds: number[]) {
    return api.post('/editor/articles/bulk-delete', { article_ids: articleIds })
  },

  // File Management Functions for Admin
  uploadFile(formData: FormData) {
    return api.post('/upload-file', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })
  },

  // List all files in system (Admin can see all files)
  getAllFiles() {
    return api.get('/admin/files')
  },

  // List user's uploaded files
  getMyFiles() {
    return api.get('/files/my-files')
  },

  // Get files by specific user (Admin only)
  getUserFiles(userId: number) {
    return api.get(`/admin/users/${userId}/files`)
  },

  // Get files by article (Admin can see all)
  getArticleFiles(articleId: number) {
    return api.get(`/admin/articles/${articleId}/files`)
  },

  // Delete file (Admin can delete any file)
  deleteFile(fileId: number) {
    return api.delete(`/editor/files/${fileId}`)
  },

  // Bulk delete files (Admin only)
  bulkDeleteFiles(fileIds: number[]) {
    return api.post('/admin/files/bulk-delete', { file_ids: fileIds })
  },

  // Get file statistics (Admin only)
  getFileStatistics() {
    return api.get('/admin/files/statistics')
  },

  // Image upload for TinyMCE (legacy support)
  uploadImage(formData: FormData) {
    return api.post('/upload-image', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })
  },

  // System Management (Admin only)
  getSystemInfo() {
    return api.get('/admin/system/info')
  },

  // Helper methods for admins
  helpers: {
    // Format user role for display
    formatUserRole(user: any): string {
      return user.roles?.[0]?.name || 'Bez role'
    },

    // Get role badge class
    getRoleBadgeClass(roleName: string): string {
      switch (roleName) {
        case 'admin': return 'admin'
        case 'editor': return 'editor'
        default: return 'no-role'
      }
    },

    // Format date for admin interface
    formatDate(dateString?: string): string {
      if (!dateString) return 'Neznámy dátum'
      return new Date(dateString).toLocaleDateString('sk-SK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },

    // Format file size
    formatFileSize(bytes: number): string {
      const units = ['B', 'KB', 'MB', 'GB']
      let size = bytes
      let unitIndex = 0
      
      while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024
        unitIndex++
      }
      
      return `${Math.round(size * 100) / 100} ${units[unitIndex]}`
    },

    // Get content preview for articles
    getContentPreview(content: string, maxLength: number = 150): string {
      if (!content) return 'Žiadny obsah...'
      const plainText = content.replace(/<[^>]*>/g, '')
      return plainText.length > maxLength ? plainText.substring(0, maxLength) + '...' : plainText
    },

    // Validate user data
    validateUser(user: UserData): string[] {
      const errors: string[] = []
      if (!user.username?.trim()) errors.push('Meno používateľa je povinné')
      if (!user.email?.trim()) errors.push('Email je povinný')
      if (!user.password?.trim() && !user.roles?.length) errors.push('Heslo je povinné pre nových používateľov')
      return errors
    },

    // Validate conference year data
    validateConferenceYear(year: ConferenceYearData): string[] {
      const errors: string[] = []
      if (!year.year?.trim()) errors.push('Rok je povinný')
      if (!year.semester?.trim()) errors.push('Semester je povinný')
      if (!['Winter', 'Summer'].includes(year.semester)) errors.push('Semester musí byť Winter alebo Summer')
      return errors
    },

    // Check if file is an image
    isImageFile(file: FileData): boolean {
      return file.mime_type?.startsWith('image/') || false
    },

    // Check if file is a document
    isDocumentFile(file: FileData): boolean {
      const docMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
      return docMimes.includes(file.mime_type) || false
    },

    // Get file icon based on type
    getFileIcon(file: FileData): string {
      if (this.isImageFile(file)) return '🖼️'
      if (this.isDocumentFile(file)) return '📄'
      return '📁'
    },

    // Generate download filename
    generateDownloadFilename(originalName: string, prefix?: string): string {
      const timestamp = new Date().toISOString().slice(0, 10)
      return prefix ? `${prefix}_${timestamp}_${originalName}` : `${timestamp}_${originalName}`
    },

    // Format conference year for display
    formatConferenceYear(conferenceYear?: { semester: string; year: string }): string {
      if (!conferenceYear) return 'Neznámy ročník'
      return `${conferenceYear.semester} ${conferenceYear.year}`
    },

    // Sort conference years by year and semester
    sortConferenceYears(years: any[]): any[] {
      return years.sort((a, b) => {
        const yearDiff = parseInt(b.year) - parseInt(a.year)
        if (yearDiff !== 0) return yearDiff
        
        const semesterOrder = { 'Winter': 0, 'Summer': 1 }
        return semesterOrder[a.semester as keyof typeof semesterOrder] - semesterOrder[b.semester as keyof typeof semesterOrder]
      })
    },

    // Get user display name
    getUserDisplayName(user: any): string {
      return user.username || user.email || 'Neznámy používateľ'
    }
  }
}