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

export const conferenceYearApi = {
  // Get all conference years
  getConferenceYears: async (): Promise<ConferenceYear[]> => {
    const response = await apiClient.get<ConferenceYearListResponse>('/conference-years')
    return response.data?.data || []
  },

  // Get conference year by ID
  getConferenceYear: async (id: string | number): Promise<ConferenceYear | null> => {
    const response = await apiClient.get<ConferenceYearResponse>(`/conference-years/${id}`)
    return response.data?.data || null
  },

  // Create new conference year
  createConferenceYear: async (data: CreateConferenceYearRequest): Promise<{ conferenceYear?: ConferenceYear; message?: string }> => {
    const response = await apiClient.post<ConferenceYearResponse>('/conference-years', data)
    return {
      conferenceYear: response.data?.data,
      message: response.data?.message
    }
  },

  // Update existing conference year
  updateConferenceYear: async (id: string | number, data: UpdateConferenceYearRequest): Promise<{ conferenceYear?: ConferenceYear; message?: string }> => {
    const response = await apiClient.put<ConferenceYearResponse>(`/conference-years/${id}`, data)
    return {
      conferenceYear: response.data?.data,
      message: response.data?.message
    }
  },

  // Delete conference year
  deleteConferenceYear: async (id: string | number): Promise<{ success: boolean; message?: string }> => {
    const response = await apiClient.delete<BaseApiResponse>(`/conference-years/${id}`)
    return {
      success: true,
      message: response.data?.message
    }
  },

  // Get active conference year
  getActiveConferenceYear: async (): Promise<ConferenceYear | null> => {
    const response = await apiClient.get<ConferenceYearResponse>('/conference-years/active/current')
    return response.data?.data || null
  },

  // Set conference year as active
  setActiveConferenceYear: async (id: string | number): Promise<{ conferenceYear?: ConferenceYear; message?: string }> => {
    const response = await apiClient.patch<ConferenceYearResponse>(`/conference-years/${id}/set-active`)
    return {
      conferenceYear: response.data?.data,
      message: response.data?.message
    }
  },

  // Get conference years by year
  getConferenceYearsByYear: async (year: string): Promise<ConferenceYear[]> => {
    const response = await apiClient.get<ConferenceYearListResponse>(`/conference-years/year/${year}`)
    return response.data?.data || []
  },

  // Get available years
  getAvailableYears: async (): Promise<string[]> => {
    const response = await apiClient.get<AvailableYearsResponse>('/conference-years/years/available')
    return response.data?.data || []
  },

  // Get conference year statistics
  getConferenceYearStatistics: async (id: string | number) => {
    const response = await apiClient.get<ConferenceYearStatisticsResponse>(`/conference-years/${id}/statistics`)
    return response.data?.data || {}
  }
}

// Conference Year helper functions
export const conferenceYearHelpers = {
  // Format conference year for display
  formatConferenceYear: (conferenceYear?: ConferenceYear): string => {
    if (!conferenceYear) return 'N/A'
    return `${conferenceYear.semester} ${conferenceYear.year}`
  },

  // Get conference year options for select dropdown
  getConferenceYearOptions: (conferenceYears?: ConferenceYear[]) => {
    if (!conferenceYears) return []
    return conferenceYears.map(cy => ({
      value: cy.id,
      label: `${cy.semester} ${cy.year}`,
      isActive: cy.is_active
    }))
  },

  // Validate conference year data before submission
  validateConferenceYear: (conferenceYear?: CreateConferenceYearRequest | UpdateConferenceYearRequest): string[] => {
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
  sortConferenceYears: (conferenceYears?: ConferenceYear[], order: 'asc' | 'desc' = 'desc'): ConferenceYear[] => {
    if (!conferenceYears) return []
    
    return [...conferenceYears].sort((a, b) => {
      const aValue = `${a.year}-${a.semester}`
      const bValue = `${b.year}-${b.semester}`
      const comparison = aValue.localeCompare(bValue)
      return order === 'desc' ? -comparison : comparison
    })
  },

  // Get active conference year from list
  getActiveFromList: (conferenceYears?: ConferenceYear[]): ConferenceYear | null => {
    if (!conferenceYears) return null
    return conferenceYears.find(cy => cy.is_active) || null
  }
}