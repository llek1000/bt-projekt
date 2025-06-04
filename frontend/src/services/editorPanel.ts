import api from './api'

interface CreateArticleRequest {
  title: string
  author_name: string
  conference_year_id: number
  content?: string
}

interface UpdateArticleRequest {
  title?: string
  author_name?: string
  conference_year_id?: number
  content?: string
}

interface Assignment {
  id: number
  user_id: number
  conference_year_id: number
  conference_year?: {
    id: number
    year: string
    semester: string
    is_active: boolean
  }
  created_at?: string
  updated_at?: string
}

export default {
  // My assignments - get current user's assignments
  getMyAssignments() {
    return api.get<{ data: Assignment[] }>('/editor/my-assignments')
  },

  // Conference years (read-only for editors)
  getConferenceYears() { 
    return api.get<{ data: any[] }>('/conference-years') 
  },

  // Articles management
  listArticles(params?: { conference_year_id?: number; search?: string }) {
    return api.get<{ data: any[] }>('/articles', { params })
  },

  createArticle(data: CreateArticleRequest) {
    return api.post<{ data: any }>('/editor/articles', data)
  },

  updateArticle(id: number, data: UpdateArticleRequest) {
    return api.put<{ data: any }>(`/editor/articles/${id}`, data)
  },

  deleteArticle(id: number) {
    return api.delete(`/editor/articles/${id}`)
  },

  bulkDeleteArticles(articleIds: number[]) {
    return api.post('/editor/articles/bulk-delete', { article_ids: articleIds })
  },

  // Image upload for TinyMCE
  uploadImage(formData: FormData) {
    return api.post<{ location: string; filename: string; path: string }>('/upload-image', formData, {
      headers: { 
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })
  },

  // File upload for TinyMCE
  uploadFile(formData: FormData) {
    return api.post<{ location: string; filename: string; file_id: number; download_url: string }>('/upload-file', formData, {
      headers: { 
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })
  },

  // Get current user info
  getCurrentUser() {
    return api.get<{ user: any }>('/user')
  },

  // Search articles
  searchArticles(query: string, params?: { conference_year_id?: number }) {
    return api.post<{ data: any[] }>('/articles/search', { query, ...params })
  },

  // Get article by ID
  getArticle(id: number) {
    return api.get<{ data: any }>(`/articles/${id}`)
  },

  // Get articles by conference year
  getArticlesByConferenceYear(conferenceYearId: number) {
    return api.get<{ data: any[]; conference_year?: any }>(`/articles/conference-year/${conferenceYearId}`)
  },

  // List user's uploaded files
  getMyFiles() {
    return api.get<{ data: any[] }>('/files/my-files')
  },

  // Delete file
  deleteFile(fileId: number) {
    return api.delete(`/editor/files/${fileId}`)
  },

  // Helper methods for editors
  helpers: {
    // Format date for display
    formatDate(dateString?: string): string {
      if (!dateString) return 'Neznámy dátum'
      
      try {
        return new Date(dateString).toLocaleDateString('sk-SK', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        })
      } catch {
        return 'Neznámy dátum'
      }
    },

    // Get content preview
    getContentPreview(content: string, maxLength: number = 150): string {
      if (!content) return 'Žiadny obsah...'
      
      // Remove HTML tags and truncate
      const plainText = content.replace(/<[^>]*>/g, '')
      return plainText.length > maxLength ? plainText.substring(0, maxLength) + '...' : plainText
    },

    // Format conference year display
    formatConferenceYear(conferenceYear?: { semester: string; year: string }): string {
      if (!conferenceYear) return 'Neznámy ročník'
      return `${conferenceYear.semester} ${conferenceYear.year}`
    },

    // Check if user is assigned to conference year
    isAssignedToYear(assignments: Assignment[], conferenceYearId: number): boolean {
      return assignments.some(assignment => assignment.conference_year_id === conferenceYearId)
    },

    // Get assigned conference year IDs
    getAssignedYearIds(assignments: Assignment[]): number[] {
      return assignments.map(assignment => assignment.conference_year_id)
    },

    // Filter articles by assigned years
    filterArticlesByAssignments(articles: any[], assignments: Assignment[]): any[] {
      const assignedYearIds = this.getAssignedYearIds(assignments)
      return articles.filter(article => assignedYearIds.includes(article.conference_year_id))
    },

    // Validate article data
    validateArticle(article: CreateArticleRequest | UpdateArticleRequest): string[] {
      const errors: string[] = []

      if ('title' in article && !article.title?.trim()) {
        errors.push('Názov článku je povinný')
      }

      if ('author_name' in article && !article.author_name?.trim()) {
        errors.push('Meno autora je povinné')
      }

      if ('conference_year_id' in article && !article.conference_year_id) {
        errors.push('Ročník konferencie je povinný')
      }

      return errors
    }
  }
}