import api from './api'
import type { ConferenceYear } from './conferenceYear'

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

export interface ArticlesByConferenceYearResponse extends BaseApiResponse {
  data?: Article[]
  conference_year?: ConferenceYear
}

export const articleApi = {
  // GET /articles
  getArticles: async (params?: { conference_year_id?: number; search?: string }): Promise<Article[]> => {
    try {
      const response = await api.get<ArticleListResponse>('/articles', { params })
      return response.data?.data || []
    } catch (error) {
      console.error('Error fetching articles:', error)
      return []
    }
  },

  // GET /articles/{id}
  getArticle: async (id: string | number): Promise<Article | null> => {
    try {
      const response = await api.get<ArticleResponse>(`/articles/${id}`)
      return response.data?.data || null
    } catch (error) {
      console.error('Error fetching article:', error)
      if (error.response?.status === 404) {
        return null
      }
      throw error
    }
  },

  // GET /articles/conference-year/{conferenceYearId}
  getArticlesByConferenceYear: async (conferenceYearId: number): Promise<{ articles?: Article[]; conferenceYear?: ConferenceYear }> => {
    try {
      const response = await api.get<ArticlesByConferenceYearResponse>(`/articles/conference-year/${conferenceYearId}`)
      return {
        articles: response.data?.data || [],
        conferenceYear: response.data?.conference_year
      }
    } catch (error) {
      console.error('Error fetching articles by conference year:', error)
      return { articles: [] }
    }
  },

  // POST /articles/search
  searchArticles: async (query: string): Promise<{ articles?: Article[]; count?: number; query?: string }> => {
    try {
      const response = await api.post<SearchResponse>('/articles/search', { query })
      return {
        articles: response.data?.data || [],
        count: response.data?.count,
        query: response.data?.query
      }
    } catch (error) {
      console.error('Error searching articles:', error)
      return { articles: [] }
    }
  }
}

// Helper functions
export const articleHelpers = {
  formatDate: (dateString?: string): string => {
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

  formatArticleSummary: (article?: Article): string => {
    if (!article?.content) return 'Žiadny obsah...'
    
    const cleanContent = article.content.replace(/<[^>]*>/g, '')
    return cleanContent.length > 150 
      ? cleanContent.substring(0, 150) + '...' 
      : cleanContent
  },

  filterArticles: (articles?: Article[], filters?: {
    search?: string
    conferenceYearId?: number
    author?: string
  }): Article[] => {
    if (!articles) return []
    
    let filtered = [...articles]
    
    if (filters?.search) {
      const query = filters.search.toLowerCase()
      filtered = filtered.filter(article => 
        article.title.toLowerCase().includes(query) ||
        article.author_name.toLowerCase().includes(query) ||
        (article.content && article.content.toLowerCase().includes(query))
      )
    }
    
    if (filters?.conferenceYearId) {
      filtered = filtered.filter(article => 
        article.conference_year_id === filters.conferenceYearId
      )
    }
    
    if (filters?.author) {
      filtered = filtered.filter(article => 
        article.author_name.toLowerCase().includes(filters.author.toLowerCase())
      )
    }
    
    return filtered
  }
}
