import api from './api'

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
  getConferenceYears(): Promise<ConferenceYearListResponse> {
    return api.get('/conference-years')
  },

  // Get conference year by ID
  getConferenceYear(id: string | number): Promise<ConferenceYearResponse> {
    return api.get(`/conference-years/${id}`)
  },

  // Create new conference year
  createConferenceYear(data: CreateConferenceYearRequest): Promise<ConferenceYearResponse> {
    return api.post('/conference-years', data)
  },

  // Update existing conference year
  updateConferenceYear(id: string | number, data: UpdateConferenceYearRequest): Promise<ConferenceYearResponse> {
    return api.put(`/conference-years/${id}`, data)
  },

  // Delete conference year
  deleteConferenceYear(id: string | number): Promise<BaseApiResponse> {
    return api.delete(`/conference-years/${id}`)
  },

  // Get active conference year
  getActiveConferenceYear(): Promise<ConferenceYearResponse> {
    return api.get('/conference-years/active/current')
  },

  // Set conference year as active
  setActiveConferenceYear(id: string | number): Promise<ConferenceYearResponse> {
    return api.patch(`/conference-years/${id}/set-active`)
  },

  // Get conference years by year
  getConferenceYearsByYear(year: string): Promise<ConferenceYearListResponse> {
    return api.get(`/conference-years/year/${year}`)
  },

  // Get available years
  getAvailableYears(): Promise<AvailableYearsResponse> {
    return api.get('/conference-years/years/available')
  },

  // Get conference year statistics
  getConferenceYearStatistics(id: string | number): Promise<ConferenceYearStatisticsResponse> {
    return api.get(`/conference-years/${id}/statistics`)
  }
}

// Helper functions
export const conferenceYearHelpers = {
  // Format conference year for display
  formatConferenceYear(conferenceYear?: ConferenceYear): string {
    if (!conferenceYear) return 'Neznámy ročník'
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
      errors.push('Údaje o ročníku konferencie sú povinné')
      return errors
    }

    if (!conferenceYear.year?.trim()) {
      errors.push('Rok je povinný')
    }

    if (conferenceYear.year && !/^\d{4}$/.test(conferenceYear.year)) {
      errors.push('Rok musí byť 4-ciferné číslo')
    }

    if (!conferenceYear.semester?.trim()) {
      errors.push('Semester je povinný')
    }

    if (conferenceYear.semester && !['Winter', 'Summer'].includes(conferenceYear.semester)) {
      errors.push('Semester musí byť Winter alebo Summer')
    }

    return errors
  },

  // Sort conference years
  sortConferenceYears(conferenceYears?: ConferenceYear[], order: 'asc' | 'desc' = 'desc'): ConferenceYear[] {
    if (!conferenceYears) return []
    
    return [...conferenceYears].sort((a, b) => {
      const yearA = parseInt(a.year)
      const yearB = parseInt(b.year)
      
      if (yearA !== yearB) {
        return order === 'desc' ? yearB - yearA : yearA - yearB
      }
      
      // If years are the same, sort by semester (Winter before Summer)
      const semesterOrder = { 'Winter': 0, 'Summer': 1 }
      const semA = semesterOrder[a.semester as keyof typeof semesterOrder]
      const semB = semesterOrder[b.semester as keyof typeof semesterOrder]
      
      return order === 'desc' ? semB - semA : semA - semB
    })
  },

  // Get active conference year from list
  getActiveFromList(conferenceYears?: ConferenceYear[]): ConferenceYear | null {
    if (!conferenceYears) return null
    return conferenceYears.find(cy => cy.is_active) || null
  }
}

// Default export for backward compatibility
export default conferenceYearApi
