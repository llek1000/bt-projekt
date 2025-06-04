import apiClient from './api'

export interface ConferenceYear {
  id: number
  year: string
  semester: string
  is_active: boolean
  created_at?: string
  updated_at?: string
}

export interface CreateConferenceYearRequest {
  year: string
  semester: string
  is_active?: boolean
}

export interface UpdateConferenceYearRequest {
  year?: string
  semester?: string
  is_active?: boolean
}

// API Response interfaces
export interface BaseApiResponse {
  message?: string
}

export interface ConferenceYearResponse extends BaseApiResponse {
  data?: ConferenceYear
}

export interface ConferenceYearListResponse extends BaseApiResponse {
  data?: ConferenceYear[]
}

export interface ConferenceYearStatisticsResponse extends BaseApiResponse {
  data?: {
    conference_year?: ConferenceYear
    statistics?: {
      total_articles?: number
      articles_by_author?: Array<{
        author_name?: string
        count?: number
      }>
      recent_articles?: Array<any>
    }
  }
}

export interface AvailableYearsResponse extends BaseApiResponse {
  data?: string[]
}

export default {
  // Get all conference years
  getConferenceYears(): Promise<ConferenceYearListResponse> {
    return apiClient.get('/conference-years')
  },

  // Get conference year by ID
  getConferenceYear(id: string | number): Promise<ConferenceYearResponse> {
    return apiClient.get(`/conference-years/${id}`)
  },

  // Create new conference year
  createConferenceYear(data: CreateConferenceYearRequest): Promise<ConferenceYearResponse> {
    return apiClient.post('/conference-years', data)
  },

  // Update existing conference year
  updateConferenceYear(id: string | number, data: UpdateConferenceYearRequest): Promise<ConferenceYearResponse> {
    return apiClient.put(`/conference-years/${id}`, data)
  },

  // Delete conference year
  deleteConferenceYear(id: string | number): Promise<BaseApiResponse> {
    return apiClient.delete(`/conference-years/${id}`)
  },

  // Get active conference year
  getActiveConferenceYear(): Promise<ConferenceYearResponse> {
    return apiClient.get('/conference-years/active/current')
  },

  // Set conference year as active
  setActiveConferenceYear(id: string | number): Promise<ConferenceYearResponse> {
    return apiClient.patch(`/conference-years/${id}/set-active`)
  },

  // Get conference years by year
  getConferenceYearsByYear(year: string): Promise<ConferenceYearListResponse> {
    return apiClient.get(`/conference-years/year/${year}`)
  },

  // Get available years
  getAvailableYears(): Promise<AvailableYearsResponse> {
    return apiClient.get('/conference-years/years/available')
  },

  // Get conference year statistics
  getConferenceYearStatistics(id: string | number): Promise<ConferenceYearStatisticsResponse> {
    return apiClient.get(`/conference-years/${id}/statistics`)
  },

  // Helper functions
  // Format conference year for display
  formatConferenceYear(conferenceYear?: ConferenceYear): string {
    if (!conferenceYear) return 'N/A'
    return `${conferenceYear.semester} ${conferenceYear.year}`
  },

  // Get conference year options for select dropdown
  getConferenceYearOptions(conferenceYears?: ConferenceYear[]) {
    if (!conferenceYears) return []
    return conferenceYears.map(cy => ({
      value: cy.id,
      label: `${cy.semester} ${cy.year}`,
      isActive: cy.is_active
    }))
  },

  // Validate conference year data before submission
  validateConferenceYear(conferenceYear?: CreateConferenceYearRequest | UpdateConferenceYearRequest): string[] {
    const errors: string[] = []

    if (!conferenceYear) {
      errors.push('Conference year data is required')
      return errors
    }

    if (!conferenceYear.year?.trim()) {
      errors.push('Year is required')
    }

    if (conferenceYear.year && !/^\d{4}$/.test(conferenceYear.year)) {
      errors.push('Year must be a valid 4-digit number')
    }

    if (!conferenceYear.semester?.trim()) {
      errors.push('Semester is required')
    }

    if (conferenceYear.semester && !['Winter', 'Summer'].includes(conferenceYear.semester)) {
      errors.push('Semester must be either Winter or Summer')
    }

    return errors
  },

  // Sort conference years
  sortConferenceYears(conferenceYears?: ConferenceYear[], order: 'asc' | 'desc' = 'desc'): ConferenceYear[] {
    if (!conferenceYears) return []

    return [...conferenceYears].sort((a, b) => {
      const aValue = `${a.year}-${a.semester}`
      const bValue = `${b.year}-${b.semester}`
      const comparison = aValue.localeCompare(bValue)
      return order === 'desc' ? -comparison : comparison
    })
  },

  // Get active conference year from list
  getActiveFromList(conferenceYears?: ConferenceYear[]): ConferenceYear | null {
    if (!conferenceYears) return null
    return conferenceYears.find(cy => cy.is_active) || null
  }
}
