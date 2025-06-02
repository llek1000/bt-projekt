import apiClient from './api'
import type { ConferenceYear } from './conferenceYear' // Import from conferenceYear service

export interface Article {
  id: number
  title: string
  content: string
  conference_year_id: number
  author_name: string
  conference_year?: ConferenceYear
  created_at?: string
  updated_at?: string
}

export interface CreateArticleRequest {
  title: string
  content?: string
  conference_year_id: number | string
  author_name: string
}

export interface UpdateArticleRequest {
  title?: string
  content?: string
  conference_year_id?: number | string
  author_name?: string
}

// API Response interfaces
export interface BaseApiResponse {
  message?: string
}

export interface ArticleResponse extends BaseApiResponse {
  data?: Article
}

export interface ArticleListResponse extends BaseApiResponse {
  data?: Article[]
}

export interface SearchResponse extends BaseApiResponse {
  data?: Article[]
  query?: string
  count?: number
}

export interface StatisticsResponse extends BaseApiResponse {
  data?: {
    total_articles?: number
    articles_by_conference_year?: Array<{
      conference_year_id?: number
      count?: number
      conference_year?: ConferenceYear
    }>
    top_authors?: Array<{
      author_name?: string
      article_count?: number
    }>
    recent_articles?: Article[]
  }
}

export interface ArticlesByConferenceYearResponse extends BaseApiResponse {
  data?: Article[]
  conference_year?: ConferenceYear
}

export interface ArticlesByAuthorResponse extends BaseApiResponse {
  data?: Article[]
  author?: string
}

export interface ExportResponse extends BaseApiResponse {
  data?: Article[]
  export_date?: string
  total_count?: number
}

export interface BulkDeleteResponse extends BaseApiResponse {
  deleted_count?: number
}

export const articleApi = {
  // GET /articles
  getArticles: async (params?: { conference_year_id?: number; search?: string }): Promise<Article[]> => {
    const response = await apiClient.get<ArticleListResponse>('/articles', { params })
    return response.data?.data || []
  },

  // GET /articles/{id}
  getArticle: async (id: string | number): Promise<Article | null> => {
    const response = await apiClient.get<ArticleResponse>(`/articles/${id}`)
    return response.data?.data || null
  },

  // POST /articles
  createArticle: async (data: CreateArticleRequest): Promise<{ article?: Article; message?: string }> => {
    const response = await apiClient.post<ArticleResponse>('/articles', data)
    return {
      article: response.data?.data,
      message: response.data?.message
    }
  },

  // PUT /articles/{id}
  updateArticle: async (id: string | number, data: UpdateArticleRequest): Promise<{ article?: Article; message?: string }> => {
    const response = await apiClient.put<ArticleResponse>(`/articles/${id}`, data)
    return {
      article: response.data?.data,
      message: response.data?.message
    }
  },

  // DELETE /articles/{id}
  deleteArticle: async (id: string | number): Promise<{ success: boolean; message?: string }> => {
    const response = await apiClient.delete<BaseApiResponse>(`/articles/${id}`)
    return {
      success: true,
      message: response.data?.message
    }
  },

  // GET /articles/conference-year/{conferenceYearId}
  getArticlesByConferenceYear: async (conferenceYearId: number): Promise<{ articles?: Article[]; conferenceYear?: ConferenceYear }> => {
    const response = await apiClient.get<ArticlesByConferenceYearResponse>(`/articles/conference-year/${conferenceYearId}`)
    return {
      articles: response.data?.data,
      conferenceYear: response.data?.conference_year
    }
  },

  // GET /articles/author/{authorName}
  getArticlesByAuthor: async (authorName: string): Promise<{ articles?: Article[]; author?: string }> => {
    const response = await apiClient.get<ArticlesByAuthorResponse>(`/articles/author/${encodeURIComponent(authorName)}`)
    return {
      articles: response.data?.data,
      author: response.data?.author
    }
  },

  // POST /articles/search
  searchArticles: async (query: string): Promise<{ articles?: Article[]; count?: number; query?: string }> => {
    const response = await apiClient.post<SearchResponse>('/articles/search', { query })
    return {
      articles: response.data?.data,
      count: response.data?.count,
      query: response.data?.query
    }
  },

  // GET /articles/stats/overview
  getStatistics: async () => {
    const response = await apiClient.get<StatisticsResponse>('/articles/stats/overview')
    return response.data?.data || {}
  },

  // POST /articles/bulk-delete
  bulkDeleteArticles: async (articleIds: number[]): Promise<{ count?: number; message?: string }> => {
    const response = await apiClient.post<BulkDeleteResponse>('/articles/bulk-delete', { article_ids: articleIds })
    return {
      count: response.data?.deleted_count,
      message: response.data?.message
    }
  },

  // GET /articles/export/json
  exportArticles: async (params?: { conference_year_id?: number }) => {
    const response = await apiClient.get<ExportResponse>('/articles/export/json', { params })
    return {
      articles: response.data?.data || [],
      exportDate: response.data?.export_date,
      totalCount: response.data?.total_count
    }
  }
}

// Article helper functions
export const articleHelpers = {
  // Validate article data before submission
  validateArticle: (article?: CreateArticleRequest | UpdateArticleRequest): string[] => {
    const errors: string[] = []
    
    if (!article) {
      errors.push('Article data is required')
      return errors
    }
    
    if (!article.title?.trim()) {
      errors.push('Title is required')
    }
    
    if (article.title && article.title.length > 255) {
      errors.push('Title cannot exceed 255 characters')
    }
    
    if (!article.author_name?.trim()) {
      errors.push('Author name is required')
    }
    
    if (article.author_name && article.author_name.length > 255) {
      errors.push('Author name cannot exceed 255 characters')
    }
    
    if (!article.conference_year_id) {
      errors.push('Conference year is required')
    }
    
    return errors
  },

  // Format date for display
  formatDate: (dateString?: string): string => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  },

  // Format article summary for lists
  formatArticleSummary: (article?: Article): string => {
    if (!article?.content) return 'No content available'
    
    const maxLength = 150
    const stripped = article.content.replace(/<[^>]*>/g, '') // Remove HTML tags
    return stripped.length > maxLength 
      ? stripped.substring(0, maxLength) + '...'
      : stripped
  },

  // Get article URL slug
  getArticleSlug: (article?: Article): string => {
    if (!article?.title) return 'untitled'
    
    return article.title
      .toLowerCase()
      .replace(/[^a-z0-9 -]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim()
  },

  // Sort articles by different criteria
  sortArticles: (articles?: Article[], sortBy: 'title' | 'author' | 'date' | 'conference_year' = 'date', order: 'asc' | 'desc' = 'desc'): Article[] => {
    if (!articles) return []
    
    return [...articles].sort((a, b) => {
      let comparison = 0
      
      switch (sortBy) {
        case 'title':
          comparison = (a.title || '').localeCompare(b.title || '')
          break
        case 'author':
          comparison = (a.author_name || '').localeCompare(b.author_name || '')
          break
        case 'date':
          const dateA = a.created_at ? new Date(a.created_at).getTime() : 0
          const dateB = b.created_at ? new Date(b.created_at).getTime() : 0
          comparison = dateA - dateB
          break
        case 'conference_year':
          const aYear = a.conference_year ? `${a.conference_year.year} ${a.conference_year.semester}` : ''
          const bYear = b.conference_year ? `${b.conference_year.year} ${b.conference_year.semester}` : ''
          comparison = aYear.localeCompare(bYear)
          break
      }
      
      return order === 'desc' ? -comparison : comparison
    })
  },

  // Filter articles by various criteria
  filterArticles: (articles?: Article[], filters?: {
    search?: string
    conferenceYearId?: number
    author?: string
  }): Article[] => {
    if (!articles) return []
    if (!filters) return articles
    
    return articles.filter(article => {
      // Search filter
      if (filters.search) {
        const searchLower = filters.search.toLowerCase()
        const matchesTitle = article.title?.toLowerCase().includes(searchLower)
        const matchesAuthor = article.author_name?.toLowerCase().includes(searchLower)
        const matchesContent = article.content?.toLowerCase().includes(searchLower)
        
        if (!matchesTitle && !matchesAuthor && !matchesContent) {
          return false
        }
      }
      
      // Conference year filter
      if (filters.conferenceYearId && article.conference_year_id !== filters.conferenceYearId) {
        return false
      }
      
      // Author filter
      if (filters.author && !article.author_name?.toLowerCase().includes(filters.author.toLowerCase())) {
        return false
      }
      
      return true
    })
  }
}