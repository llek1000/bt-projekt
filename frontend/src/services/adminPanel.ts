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
    const queryString = params ? '?' + new URLSearchParams(
      Object.entries(params).reduce((acc, [key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          acc[key] = String(value);
        }
        return acc;
      }, {} as Record<string, string>)
    ).toString() : '';
    
    return api.get<{ users: UserData[] }>(`/admin/users${queryString}`);
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
    const queryString = params ? '?' + new URLSearchParams(
      Object.entries(params).reduce((acc, [key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          acc[key] = String(value);
        }
        return acc;
      }, {} as Record<string, string>)
    ).toString() : '';
    
    return api.get<{ roles: RoleData[] }>(`/admin/roles${queryString}`);
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

  // Get all users with editor role for assignments
  getEditors() {
    return api.get<{ data: any[] }>('/admin/users?role=editor');
  },

  // Get all assignments
  getAllAssignments() {
    return api.get<{ data: any[] }>('/admin/assignments');
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

  // File Management Functions for Admin
  uploadFile(formData: FormData) {
    return api.post<{ location: string; filename: string; file_id: number; download_url: string }>('/upload-file', formData, {
      headers: { 
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })
  },

  // List all files in system (Admin can see all files)
  getAllFiles() {
    return api.get<{ data: FileData[] }>('/admin/files')
  },

  // List user's uploaded files
  getMyFiles() {
    return api.get<{ data: FileData[] }>('/files/my-files')
  },

  // Get files by specific user (Admin only)
  getUserFiles(userId: number) {
    return api.get<{ data: FileData[] }>(`/admin/users/${userId}/files`)
  },

  // Get files by article (Admin can see all)
  getArticleFiles(articleId: number) {
    return api.get<{ data: FileData[] }>(`/admin/articles/${articleId}/files`)
  },

  // Delete file (Admin can delete any file)
  deleteFile(fileId: number) {
    return api.delete(`/admin/files/${fileId}`)
  },

  // Bulk delete files (Admin only)
  bulkDeleteFiles(fileIds: number[]) {
    return api.post('/admin/files/bulk-delete', { file_ids: fileIds })
  },

  // Get file statistics (Admin only)
  getFileStatistics() {
    return api.get<{ data: any }>('/admin/files/statistics')
  },

  // Image upload for TinyMCE (legacy support)
  uploadImage(formData: FormData) {
    return api.post<{ location: string; filename: string; path: string }>('/upload-image', formData, {
      headers: { 
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })
  },

  // Subpages Management (Admin only)
  listSubpages(yearId: number) {
    return api.get<{ data: any[] }>(`/admin/conference-years/${yearId}/subpages`)
  },
  
  createSubpage(yearId: number, data: any) {
    return api.post<{ data: any }>(`/admin/conference-years/${yearId}/subpages`, data)
  },
  
  updateSubpage(id: number, data: any) {
    return api.put<{ data: any }>(`/admin/subpages/${id}`, data)
  },
  
  deleteSubpage(id: number) {
    return api.delete(`/admin/subpages/${id}`)
  },

  // Statistics and Reports (Admin only)
  getUserStatistics() {
    return api.get<{ data: any }>('/admin/statistics/users')
  },

  getArticleStatistics() {
    return api.get<{ data: any }>('/admin/statistics/articles')
  },

  getConferenceYearStatistics(yearId: number) {
    return api.get<{ data: any }>(`/admin/statistics/conference-years/${yearId}`)
  },

  // System Management (Admin only)
  getSystemInfo() {
    return api.get<{ data: any }>('/admin/system/info')
  },

  // Backup and maintenance (Admin only)
  createBackup() {
    return api.post<{ data: any }>('/admin/system/backup')
  },

  getBackupList() {
    return api.get<{ data: any[] }>('/admin/system/backups')
  },

  downloadBackup(backupId: string) {
    return api.get(`/admin/system/backups/${backupId}/download`, {
      responseType: 'blob'
    })
  },

  deleteBackup(backupId: string) {
    return api.delete(`/admin/system/backups/${backupId}`)
  },

  // Advanced file operations (Admin only)
  moveFile(fileId: number, newCategoryOrPath: string) {
    return api.patch(`/admin/files/${fileId}/move`, { 
      category: newCategoryOrPath 
    })
  },

  duplicateFile(fileId: number) {
    return api.post(`/admin/files/${fileId}/duplicate`)
  },

  // File cleanup operations (Admin only)
  cleanupOrphanedFiles() {
    return api.post('/admin/files/cleanup/orphaned')
  },

  cleanupOldFiles(olderThanDays: number) {
    return api.post('/admin/files/cleanup/old', { 
      older_than_days: olderThanDays 
    })
  },

  // User activity logs (Admin only)
  getUserActivityLogs(userId?: number, limit?: number) {
    const params: any = {};
    if (userId) params.user_id = userId;
    if (limit) params.limit = limit;
    
    return api.get<{ data: any[] }>('/admin/logs/user-activity', { params })
  },

  // System logs (Admin only)
  getSystemLogs(type?: string, limit?: number) {
    const params: any = {};
    if (type) params.type = type;
    if (limit) params.limit = limit;
    
    return api.get<{ data: any[] }>('/admin/logs/system', { params })
  },

  // Export data (Admin only)
  exportUsers(format: 'csv' | 'json' | 'xlsx' = 'csv') {
    return api.get(`/admin/export/users?format=${format}`, {
      responseType: 'blob'
    })
  },

  exportArticles(format: 'csv' | 'json' | 'xlsx' = 'csv', conferenceYearId?: number) {
    const params = new URLSearchParams({ format });
    if (conferenceYearId) params.append('conference_year_id', conferenceYearId.toString());
    
    return api.get(`/admin/export/articles?${params.toString()}`, {
      responseType: 'blob'
    })
  },

  exportFiles(format: 'csv' | 'json' | 'xlsx' = 'csv') {
    return api.get(`/admin/export/files?format=${format}`, {
      responseType: 'blob'
    })
  },

  // Bulk operations for users (Admin only)
  bulkUpdateUsers(userIds: number[], updateData: Partial<UserData>) {
    return api.patch('/admin/users/bulk-update', {
      user_ids: userIds,
      update_data: updateData
    })
  },

  bulkDeleteUsers(userIds: number[]) {
    return api.post('/admin/users/bulk-delete', {
      user_ids: userIds
    })
  },

  // Email and notifications (Admin only)
  sendBulkEmail(userIds: number[], subject: string, message: string) {
    return api.post('/admin/email/bulk-send', {
      user_ids: userIds,
      subject,
      message
    })
  },

  sendSystemNotification(title: string, message: string, userIds?: number[]) {
    return api.post('/admin/notifications/send', {
      title,
      message,
      user_ids: userIds
    })
  },

  // Helper methods for admins
  helpers: {
    // Format user role for display
    formatUserRole(user: any): string {
      if (!user || !user.roles || user.roles.length === 0) {
        return 'Bez role'
      }
      
      return user.roles.map((role: any) => role.name).join(', ')
    },

    // Get role badge class
    getRoleBadgeClass(roleName: string): string {
      const roleClasses: Record<string, string> = {
        'admin': 'badge-admin',
        'editor': 'badge-editor',
        'user': 'badge-user',
        'moderator': 'badge-moderator'
      }
      
      return roleClasses[roleName?.toLowerCase()] || 'badge-default'
    },

    // Format date for admin interface
    formatDate(dateString?: string): string {
      if (!dateString) return 'Neznámy dátum'
      
      try {
        return new Date(dateString).toLocaleDateString('sk-SK', {
          year: 'numeric',
          month: 'long', 
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        })
      } catch {
        return 'Neplatný dátum'
      }
    },

    // Format file size
    formatFileSize(bytes: number): string {
      if (bytes === 0) return '0 B'
      
      const units = ['B', 'KB', 'MB', 'GB', 'TB']
      const k = 1024
      const dm = 2
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + units[i]
    },

    // Get content preview for articles
    getContentPreview(content: string, maxLength: number = 150): string {
      if (!content) return 'Bez obsahu'
      
      // Remove HTML tags
      const cleanContent = content.replace(/<[^>]*>/g, '').trim()
      
      if (cleanContent.length <= maxLength) return cleanContent
      
      return cleanContent.substring(0, maxLength) + '...'
    },

    // Validate user data
    validateUser(user: UserData): string[] {
      const errors: string[] = []

      if (!user.username || user.username.trim().length === 0) {
        errors.push('Používateľské meno je povinné')
      }

      if (!user.email || user.email.trim().length === 0) {
        errors.push('Email je povinný')
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(user.email)) {
        errors.push('Email musí mať platný formát')
      }

      if (user.password && user.password.length < 8) {
        errors.push('Heslo musí mať minimálne 8 znakov')
      }

      if (user.password && user.password !== user.password_confirmation) {
        errors.push('Heslá sa nezhodujú')
      }

      return errors
    },

    // Validate conference year data
    validateConferenceYear(year: ConferenceYearData): string[] {
      const errors: string[] = []

      if (!year.year || year.year.trim().length === 0) {
        errors.push('Rok je povinný')
      } else if (!/^\d{4}$/.test(year.year)) {
        errors.push('Rok musí byť 4-ciferné číslo')
      }

      if (!year.semester) {
        errors.push('Semester je povinný')
      } else if (!['Winter', 'Summer'].includes(year.semester)) {
        errors.push('Semester musí byť Winter alebo Summer')
      }

      return errors
    },

    // Check if file is an image
    isImageFile(file: FileData): boolean {
      return file.mime_type?.startsWith('image/') || false
    },

    // Check if file is a document
    isDocumentFile(file: FileData): boolean {
      const documentTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain'
      ]
      
      return documentTypes.includes(file.mime_type || '')
    },

    // Get file icon based on type
    getFileIcon(file: FileData): string {
      if (this.isImageFile(file)) return '🖼️'
      if (file.mime_type === 'application/pdf') return '📄'
      if (this.isDocumentFile(file)) return '📝'
      return '📎'
    },

    // Generate download filename
    generateDownloadFilename(originalName: string, prefix?: string): string {
      const timestamp = new Date().toISOString().slice(0, 10)
      const cleanName = originalName.replace(/[^a-zA-Z0-9.-]/g, '_')
      
      return prefix ? `${prefix}_${timestamp}_${cleanName}` : `${timestamp}_${cleanName}`
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
        
        // If same year, Winter comes before Summer
        if (a.semester === 'Winter' && b.semester === 'Summer') return -1
        if (a.semester === 'Summer' && b.semester === 'Winter') return 1
        
        return 0
      })
    },

    // Get user display name
    getUserDisplayName(user: any): string {
      return user?.username || user?.email || 'Neznámy používateľ'
    },

    // Check if user has admin role
    isAdmin(user: any): boolean {
      return user?.roles?.some((role: any) => role.name.toLowerCase() === 'admin') || false
    },

    // Check if user has editor role
    isEditor(user: any): boolean {
      return user?.roles?.some((role: any) => role.name.toLowerCase() === 'editor') || false
    },

    // Get system status color
    getSystemStatusColor(status: string): string {
      const statusColors: Record<string, string> = {
        'online': '#10b981',
        'maintenance': '#f59e0b', 
        'offline': '#ef4444',
        'warning': '#f59e0b'
      }
      
      return statusColors[status.toLowerCase()] || '#6b7280'
    }
  }
}