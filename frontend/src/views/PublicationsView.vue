<template>
  <div class="page-container">
    <NavbarComponent />
    
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="hero-particles"></div>
      </div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">
            <span class="title-line">Vedecké</span>
            <span class="title-line highlight">Publikácie</span>
          </h1>
          <p class="hero-subtitle">
            Preskúmajte našu rozsiahlu zbierku vedeckých článkov a výskumných prác
          </p>
          <div class="hero-stats">
            <div class="stat-item">
              <div class="stat-number">{{ articles.length }}</div>
              <div class="stat-label">Publikácií</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">{{ conferenceYears.length }}</div>
              <div class="stat-label">Ročníkov</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">{{ uniqueAuthors.length }}</div>
              <div class="stat-label">Autorov</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
      <div class="container">
        <div class="filter-wrapper">
          <!-- Search -->
          <div class="search-container">
            <div class="search-input-wrapper">
              <span class="search-icon">🔍</span>
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Hľadať v publikáciách..." 
                class="search-input"
              />
              <button 
                v-if="searchQuery" 
                @click="searchQuery = ''" 
                class="clear-button"
              >
                ✕
              </button>
            </div>
          </div>

          <!-- Filters -->
          <div class="filter-controls">
            <div class="filter-group">
              <label>Ročník:</label>
              <select v-model="selectedYear" class="filter-select">
                <option value="">Všetky ročníky</option>
                <option v-for="year in availableYears" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
            </div>

            <div class="filter-group">
              <label>Semester:</label>
              <select v-model="selectedSemester" class="filter-select">
                <option value="">Všetky semestre</option>
                <option value="Winter">Winter</option>
                <option value="Summer">Summer</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Active Filters -->
        <div v-if="hasActiveFilters" class="active-filters">
          <span class="filter-label">Aktívne filtre:</span>
          <div class="filter-tags">
            <span v-if="searchQuery" class="filter-tag">
              Hľadanie: "{{ searchQuery }}"
              <button @click="searchQuery = ''" class="remove-filter">×</button>
            </span>
            <span v-if="selectedYear" class="filter-tag">
              Rok: {{ selectedYear }}
              <button @click="selectedYear = ''" class="remove-filter">×</button>
            </span>
            <span v-if="selectedSemester" class="filter-tag">
              Semester: {{ selectedSemester }}
              <button @click="selectedSemester = ''" class="remove-filter">×</button>
            </span>
          </div>
          <button @click="clearAllFilters" class="clear-all-filters">
            Vymazať všetky filtre
          </button>
        </div>
      </div>
    </section>

    <main class="main-content">
      <section class="publications-content">
        <div class="container">
          <!-- Loading stav -->
          <div v-if="loading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Načítavajú sa publikácie...</p>
          </div>

          <!-- Error stav -->
          <div v-else-if="error" class="error-state">
            <div class="error-icon">⚠️</div>
            <h3>Chyba pri načítavaní</h3>
            <p>{{ error }}</p>
            <button @click="loadData" class="retry-button">Skúsiť znovu</button>
          </div>

          <!-- Prázdny stav -->
          <div v-else-if="filteredArticles.length === 0" class="empty-state">
            <div class="empty-icon">📄</div>
            <h3>Žiadne publikácie</h3>
            <p v-if="hasActiveFilters">
              Nenašli sa žiadne publikácie pre zadané kritériá.
            </p>
            <p v-else>
              Momentálne nie sú k dispozícii žiadne publikácie.
            </p>
            <button 
              v-if="hasActiveFilters" 
              @click="clearAllFilters" 
              class="clear-filters-button"
            >
              Vymazať filtre
            </button>
          </div>

          <!-- Publikácie grid -->
          <div v-else>
            <div class="results-summary">
              <h2>
                {{ filteredArticles.length }} 
                {{ filteredArticles.length === 1 ? 'publikácia' : 'publikácií' }}
              </h2>
            </div>

            <div class="publications-grid">
              <div 
                v-for="publication in paginatedPublications" 
                :key="publication.id" 
                class="publication-card"
              >
                <div class="card-header">
                  <span class="type-badge">Vedecký článok</span>
                  <span class="publication-date">
                    {{ formatDate(publication.created_at) }}
                  </span>
                </div>
                
                <div class="card-body">
                  <h3 class="publication-title">{{ publication.title }}</h3>
                  
                  <div class="publication-author">
                    <span class="author-icon">👤</span>
                    <span>{{ publication.author_name }}</span>
                  </div>
                  
                  <div class="publication-meta">
                    <div class="conference-info">
                      <span class="conference-icon">📅</span>
                      <span>
                        {{ publication.conference_year?.semester }} 
                        {{ publication.conference_year?.year }}
                      </span>
                    </div>
                  </div>

                  <div class="publication-abstract">
                    {{ getExcerpt(publication.content) }}
                  </div>
                </div>
                
                <div class="card-footer">
                  <router-link 
                    :to="`/articles/${publication.id}`" 
                    class="read-more-btn"
                  >
                    Čítať celý článok
                    <span class="arrow">→</span>
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Paginácia -->
            <div v-if="totalPages > 1" class="pagination-wrapper">
              <div class="pagination">
                <div class="page-numbers">
                  <button 
                    @click="currentPage = 1" 
                    :disabled="currentPage === 1"
                    class="page-btn"
                  >
                    Prvá
                  </button>
                  <button 
                    @click="currentPage--" 
                    :disabled="currentPage === 1"
                    class="page-btn"
                  >
                    ‹
                  </button>
                  
                  <button 
                    v-for="page in visiblePages" 
                    :key="page"
                    @click="currentPage = page"
                    :class="{ active: currentPage === page }"
                    class="page-btn"
                  >
                    {{ page }}
                  </button>
                  
                  <button 
                    @click="currentPage++" 
                    :disabled="currentPage === totalPages"
                    class="page-btn"
                  >
                    ›
                  </button>
                  <button 
                    @click="currentPage = totalPages" 
                    :disabled="currentPage === totalPages"
                    class="page-btn"
                  >
                    Posledná
                  </button>
                </div>
                
                <div class="pagination-info">
                  Stránka {{ currentPage }} z {{ totalPages }} 
                  ({{ filteredArticles.length }} celkom)
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <FooterComponent />
  </div>
</template>

<script>
import { articleApi } from '@/services/article'
import { conferenceYearApi } from '@/services/conferenceYear'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

export default {
  name: 'PublicationsView',
  
  components: {
    NavbarComponent,
    FooterComponent
  },
  
  data() {
    return {
      articles: [],
      conferenceYears: [],
      loading: true,
      error: null,
      
      // Filter states
      searchQuery: '',
      selectedYear: '',
      selectedSemester: '',
      
      // Pagination
      currentPage: 1,
      itemsPerPage: 12
    }
  },
  
  computed: {
    // Filtered articles based on search and filters
    filteredArticles() {
      let filtered = [...this.articles]
      
      // Search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(article => 
          article.title.toLowerCase().includes(query) ||
          article.author_name.toLowerCase().includes(query) ||
          (article.content && article.content.toLowerCase().includes(query))
        )
      }
      
      // Year filter
      if (this.selectedYear) {
        filtered = filtered.filter(article => 
          article.conference_year?.year === this.selectedYear
        )
      }
      
      // Semester filter
      if (this.selectedSemester) {
        filtered = filtered.filter(article => 
          article.conference_year?.semester === this.selectedSemester
        )
      }
      
      return filtered
    },

    // Paginated articles
    paginatedPublications() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredArticles.slice(start, end)
    },

    // Pagination info
    totalPages() {
      return Math.ceil(this.filteredArticles.length / this.itemsPerPage)
    },

    visiblePages() {
      const pages = []
      const start = Math.max(1, this.currentPage - 2)
      const end = Math.min(this.totalPages, this.currentPage + 2)
      
      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      return pages
    },

    // Available filter options
    availableYears() {
      const years = new Set()
      this.articles.forEach(article => {
        if (article.conference_year?.year) {
          years.add(article.conference_year.year)
        }
      })
      return Array.from(years).sort().reverse()
    },

    uniqueAuthors() {
      const authors = new Set()
      this.articles.forEach(article => {
        if (article.author_name) {
          authors.add(article.author_name)
        }
      })
      return Array.from(authors)
    },

    // Check if any filters are active
    hasActiveFilters() {
      return !!(this.searchQuery || this.selectedYear || this.selectedSemester)
    }
  },

  watch: {
    // Reset to first page when filters change
    searchQuery() {
      this.currentPage = 1
    },
    selectedYear() {
      this.currentPage = 1
    },
    selectedSemester() {
      this.currentPage = 1
    }
  },
  
  mounted() {
    this.loadData()
  },
  
  methods: {
    async loadData() {
      try {
        this.loading = true
        this.error = null
        
        const [articlesData, yearsData] = await Promise.all([
          articleApi.getArticles(),
          conferenceYearApi.getConferenceYears()
        ])
        
        this.articles = articlesData || []
        this.conferenceYears = yearsData || []
        
      } catch (error) {
        console.error('Error loading data:', error)
        this.error = 'Nepodarilo sa načítať publikácie. Skúste to znovu.'
      } finally {
        this.loading = false
      }
    },

    formatDate(dateString) {
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

    getExcerpt(content) {
      if (!content) return 'Žiadny obsah dostupný...'
      
      // Remove HTML tags and limit to 150 characters
      const plainText = content.replace(/<[^>]*>/g, '')
      return plainText.length > 150 
        ? plainText.substring(0, 150) + '...'
        : plainText
    },

    clearAllFilters() {
      this.searchQuery = ''
      this.selectedYear = ''
      this.selectedSemester = ''
      this.currentPage = 1
    }
  }
}
</script>

<style scoped>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

/* CSS Variables */
:root {
  --primary-color: #2563eb;
  --primary-dark: #1d4ed8;
  --primary-light: #3b82f6;
  --secondary-color: #f59e0b;
  --accent-color: #10b981;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --text-light: #9ca3af;
  --bg-primary: #ffffff;
  --bg-secondary: #f8fafc;
  --bg-dark: #0f172a;
  --border-color: #e5e7eb;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

/* Base Styles */
* {
  box-sizing: border-box;
}

.page-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: 'Inter', sans-serif;
  color: var(--text-primary);
  line-height: 1.6;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.main-content {
  flex: 1;
}

/* Hero Section */
.hero-section {
  position: relative;
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: var(--gradient-primary);
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
}

.hero-particles {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1" fill="white" opacity="0.3"><animate attributeName="opacity" values="0.3;1;0.3" dur="2s" repeatCount="indefinite"/></circle><circle cx="80" cy="40" r="1" fill="white" opacity="0.4"><animate attributeName="opacity" values="0.4;1;0.4" dur="3s" repeatCount="indefinite"/></circle><circle cx="40" cy="80" r="1" fill="white" opacity="0.2"><animate attributeName="opacity" values="0.2;1;0.2" dur="4s" repeatCount="indefinite"/></circle></svg>') repeat;
}

.hero-content {
  position: relative;
  z-index: 10;
  text-align: center;
  color: white;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 2rem;
}

.hero-title {
  font-family: 'Poppins', sans-serif;
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.title-line {
  display: block;
}

.title-line.highlight {
  background: linear-gradient(45deg, #ffd700, #ffed4e);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 1.25rem;
  margin-bottom: 3rem;
  opacity: 0.9;
  line-height: 1.6;
}

.hero-stats {
  display: flex;
  justify-content: center;
  gap: 4rem;
  margin-top: 2rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  display: block;
  font-size: 2.5rem;
  font-weight: 700;
  font-family: 'Poppins', sans-serif;
  color: #ffd700;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 1rem;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Filter Section */
.filter-section {
  padding: 3rem 0;
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
}

.filter-wrapper {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.search-container {
  display: flex;
  justify-content: center;
}

.search-input-wrapper {
  position: relative;
  max-width: 600px;
  width: 100%;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-light);
  font-size: 1.2rem;
}

.filter-section .search-input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid var(--border-color);
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
}

.filter-section .search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.clear-button {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--text-light);
  cursor: pointer;
  font-size: 1.2rem;
  transition: color 0.3s ease;
}

.clear-button:hover {
  color: var(--text-primary);
}

.filter-controls {
  display: flex;
  justify-content: center;
  gap: 2rem;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  min-width: 200px;
}

.filter-group label {
  font-weight: 500;
  color: var(--text-primary);
  font-size: 0.9rem;
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  background: white;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Active Filters */
.active-filters {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  margin-top: 1rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
}

.filter-label {
  font-weight: 500;
  color: var(--text-primary);
}

.filter-tags {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.filter-tag {
  background: var(--primary-color);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.remove-filter {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  transition: background 0.3s ease;
}

.remove-filter:hover {
  background: rgba(255, 255, 255, 0.2);
}

.clear-all-filters {
  background: var(--text-light);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
  transition: background 0.3s ease;
}

.clear-all-filters:hover {
  background: var(--text-secondary);
}

/* Publications Content */
.publications-content {
  padding: 4rem 0;
  min-height: 50vh;
}

.results-summary h2 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 2rem;
}

/* Loading, Error, Empty States */
.loading-state,
.error-state,
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--border-color);
  border-top-color: var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

.error-icon,
.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.error-state h3,
.empty-state h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.retry-button,
.clear-filters-button {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.3s ease;
  margin-top: 1rem;
}

.retry-button:hover,
.clear-filters-button:hover {
  background: var(--primary-dark);
}

/* Publications Grid */
.publications-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.publication-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.publication-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.type-badge {
  background: var(--gradient-primary);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.publication-date {
  color: var(--text-light);
  font-size: 0.875rem;
}

.card-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.publication-title {
  font-family: 'Poppins', sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.4;
  margin: 0;
}

.publication-author {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.author-icon {
  font-size: 1rem;
}

.publication-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-top: auto;
}

.conference-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-light);
  font-size: 0.875rem;
}

.conference-icon {
  font-size: 1rem;
}

.publication-abstract {
  color: var(--text-secondary);
  line-height: 1.6;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.card-footer {
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

.read-more-btn {
  background: none;
  border: none;
  color: var(--primary-color);
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  padding: 0;
  font-size: 0.95rem;
}

.read-more-btn:hover {
  color: var(--primary-dark);
  transform: translateX(3px);
}

.arrow {
  transition: transform 0.3s ease;
}

.read-more-btn:hover .arrow {
  transform: translateX(3px);
}

/* Pagination */
.pagination-wrapper {
  margin-top: 4rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.page-numbers {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.page-btn {
  background: white;
  border: 1px solid var(--border-color);
  color: var(--text-primary);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  min-width: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-btn:hover:not(:disabled) {
  background: var(--bg-secondary);
  border-color: var(--primary-color);
}

.page-btn.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-info {
  color: var(--text-secondary);
  font-size: 0.9rem;
}
</style>