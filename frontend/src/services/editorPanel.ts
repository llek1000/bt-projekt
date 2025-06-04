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
  getMyAssignments() {
    return api.get<{ data: Assignment[] }>('/editor/my-assignments')
  },

  getConferenceYears() {
    return api.get<{ data: any[] }>('/conference-years')
  },

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

  uploadImage(formData: FormData) {
    return api.post<{ location: string; filename: string; path: string }>('/upload-image', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })
  },

  uploadFile(formData: FormData) {
    return api.post<{ location: string; filename: string; file_id: number; download_url: string }>('/upload-file', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })
  },


  getCurrentUser() {
    return api.get<{ user: any }>('/user')
  },


  searchArticles(query: string, params?: { conference_year_id?: number }) {
    return api.post<{ data: any[] }>('/articles/search', { query, ...params })
  },


  getArticle(id: number) {
    return api.get<{ data: any }>(`/articles/${id}`)
  },


  getArticlesByConferenceYear(conferenceYearId: number) {
    return api.get<{ data: any[]; conference_year?: any }>(`/articles/conference-year/${conferenceYearId}`)
  },

 
  getMyFiles() {
    return api.get<{ data: any[] }>('/files/my-files')
  },


  deleteFile(fileId: number) {
    return api.delete(`/editor/files/${fileId}`)
  },


  helpers: {
    formatDate(dateString?: string): string {
      if (!dateString) return 'Neznámy dátum'
      return new Date(dateString).toLocaleDateString('sk-SK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },

    getContentPreview(content: string, maxLength: number = 150): string {
      if (!content) return 'Žiadny obsah...'

      const plainText = content.replace(/<[^>]*>/g, '')
      return plainText.length > maxLength ? plainText.substring(0, maxLength) + '...' : plainText
    },

    formatConferenceYear(conferenceYear?: { semester: string; year: string }): string {
      if (!conferenceYear) return 'Neznámy ročník'
      return `${conferenceYear.semester} ${conferenceYear.year}`
    },

  
    isAssignedToYear(assignments: Assignment[], conferenceYearId: number): boolean {
      return assignments.some(assignment => assignment.conference_year_id === conferenceYearId)
    },


    getAssignedYearIds(assignments: Assignment[]): number[] {
      return assignments.map(assignment => assignment.conference_year_id)
    },

    filterArticlesByAssignments(articles: any[], assignments: Assignment[]): any[] {
      const assignedYearIds = this.getAssignedYearIds(assignments)
      return articles.filter(article => assignedYearIds.includes(article.conference_year_id))
    }
  }
}
