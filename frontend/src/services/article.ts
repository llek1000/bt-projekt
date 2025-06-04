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

export default {
  // GET /articles
  getArticles(params?: { conference_year_id?: number; search?: string }): Promise<ArticleListResponse> {
    return apiClient.get('/articles', { params })
  },

  // GET /articles/{id}
  getArticle(id: string | number): Promise<ArticleResponse> {
    return apiClient.get(`/articles/${id}`)
  },

  // POST /articles
  createArticle(data: CreateArticleRequest): Promise<ArticleResponse> {
    return apiClient.post('/articles', data)
  },

  // PUT /articles/{id}
  updateArticle(id: string | number, data: UpdateArticleRequest): Promise<ArticleResponse> {
    return apiClient.put(`/articles/${id}`, data)
  },

  // DELETE /articles/{id}
  deleteArticle(id: string | number): Promise<BaseApiResponse> {
    return apiClient.delete(`/articles/${id}`)
  },

  // GET /articles/conference-year/{conferenceYearId}
  getArticlesByConferenceYear(conferenceYearId: number): Promise<ArticlesByConferenceYearResponse> {
    return apiClient.get(`/articles/conference-year/${conferenceYearId}`)
  },

  // GET /articles/author/{authorName}
  getArticlesByAuthor(authorName: string): Promise<ArticlesByAuthorResponse> {
    return apiClient.get(`/articles/author/${encodeURIComponent(authorName)}`)
  },

  // POST /articles/search
  searchArticles(query: string): Promise<SearchResponse> {
    return apiClient.post('/articles/search', { query })
  },

  // GET /articles/stats/overview
  getStatistics(): Promise<StatisticsResponse> {
    return apiClient.get('/articles/stats/overview')
  },

  // POST /articles/bulk-delete
  bulkDeleteArticles(articleIds: number[]): Promise<BulkDeleteResponse> {
    return apiClient.post('/articles/bulk-delete', { article_ids: articleIds })
  },

  // GET /articles/export/json
  exportArticles(params?: { conference_year_id?: number }): Promise<ExportResponse> {
    return apiClient.get('/articles/export/json', { params })
  },

  // Helper functions
  validateArticle(article?: CreateArticleRequest | UpdateArticleRequest): string[] {
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

  formatDate(dateString?: string): string {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  },

  formatArticleSummary(article?: Article): string {
    if (!article?.content) return 'No content available'

    const maxLength = 150
    const stripped = article.content.replace(/<[^>]*>/g, '') // Remove HTML tags
    return stripped.length > maxLength
      ? stripped.substring(0, maxLength) + '...'
      : stripped
  },

  getArticleSlug(article?: Article): string {
    if (!article?.title) return 'untitled'

    return article.title
      .toLowerCase()
      .replace(/[^a-z0-9 -]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim()
  }
}
