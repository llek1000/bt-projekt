<template>
  <div class="page-container">
    <NavbarComponent />
    
    <main class="main-content">
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
              Preskúmajte našu zbierku vedeckých článkov a výskumných prác z rôznych konferencií a ročníkov.
            </p>
            <div class="hero-stats">
              <div class="stat-item">
                <span class="stat-number">{{ totalArticles }}</span>
                <span class="stat-label">Celkovo článkov</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">{{ conferenceYears.length }}</span>
                <span class="stat-label">Ročníkov</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">{{ uniqueAuthors }}</span>
                <span class="stat-label">Autorov</span>
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
                <i class="search-icon">🔍</i>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Hľadať články, autorov..."
                  class="search-input"
                />
                <button 
                  v-if="searchQuery" 
                  @click="clearSearch"
                  class="clear-button"
                >
                  ✕
                </button>
              </div>
            </div>

            <!-- Filters -->
            <div class="filter-controls">
              <div class="filter-group">
                <label for="conferenceYear">Ročník:</label>
                <select 
                  id="conferenceYear" 
                  v-model="selectedConferenceYear"
                  class="filter-select"
                >
                  <option value="">Všetky ročníky</option>
                  <option 
                    v-for="year in conferenceYears" 
                    :key="year.id" 
                    :value="year.id"
                  >
                    {{ year.semester }} {{ year.year }}
                  </option>
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
                <button @click="clearSearch" class="remove-filter">✕</button>
              </span>
              <span v-if="selectedConferenceYear" class="filter-tag">
                Ročník: {{ getConferenceYearName(selectedConferenceYear) }}
                <button @click="selectedConferenceYear = ''" class="remove-filter">✕</button>
              </span>
            </div>
            <button @click="clearAllFilters" class="clear-all-filters">
              Vymazať všetky filtre
            </button>
          </div>
        </div>
      </section>

      <!-- Publications Content -->
      <section class="publications-content">
        <div class="container">
          <div class="results-summary">
            <h2>
              {{ filteredArticles.length }} 
              {{ filteredArticles.length === 1 ? 'článok' : 'článkov' }}
              <span v-if="hasActiveFilters">vyhovuje kritériám</span>
            </h2>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Načítavam publikácie...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="error-state">
            <div class="error-icon">⚠️</div>
            <h3>Chyba pri načítavaní</h3>
            <p>{{ error }}</p>
            <button @click="loadData" class="retry-button">
              Skúsiť znovu
            </button>
          </div>

          <!-- Empty State -->
          <div v-else-if="filteredArticles.length === 0" class="empty-state">
            <div class="empty-icon">📄</div>
            <h3>Žiadne publikácie</h3>
            <p v-if="hasActiveFilters">
              Neboli nájdené žiadne publikácie pre zadané kritériá.
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

          <!-- Publications Grid -->
          <div v-else class="publications-grid">
            <article 
              v-for="article in paginatedArticles" 
              :key="article.id" 
              class="publication-card"
            >
              <div class="card-header">
                <div class="type-badge">Článok</div>
                <div class="publication-date">
                  {{ formatDate(article.created_at) }}
                </div>
              </div>
              
              <div class="card-body">
                <h3 class="publication-title">
                  {{ article.title }}
                </h3>
                
                <div class="publication-author">
                  <i class="author-icon">👤</i>
                  {{ article.author_name }}
                </div>
                
                <div class="publication-date-info">
                  <i class="date-icon">📅</i>
                  Publikované: {{ formatDate(article.created_at) }}
                </div>
                
                <div class="publication-meta">
                  <div class="conference-info">
                    <i class="conference-icon">🎓</i>
                    {{ article.conference_year?.semester }} {{ article.conference_year?.year }}
                  </div>
                </div>
                
                <div class="publication-abstract">
                  {{ getArticlePreview(article) }}
                </div>
              </div>
              
              <div class="card-footer">
                <router-link 
                  :to="`/articles/${article.id}`" 
                  class="read-more-btn"
                >
                  Čítať článok
                  <span class="arrow">→</span>
                </router-link>
              </div>
            </article>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="pagination-wrapper">
            <div class="pagination">
              <div class="page-numbers">
                <button 
                  :disabled="currentPage === 1"
                  @click="currentPage = 1"
                  class="page-btn"
                >
                  Prvá
                </button>
                
                <button 
                  :disabled="currentPage === 1"
                  @click="currentPage--"
                  class="page-btn"
                >
                  Predchádzajúca
                </button>
                
                <span class="pagination-info">
                  Stránka {{ currentPage }} z {{ totalPages }}
                </span>
                
                <button 
                  :disabled="currentPage === totalPages"
                  @click="currentPage++"
                  class="page-btn"
                >
                  Nasledujúca
                </button>
                
                <button 
                  :disabled="currentPage === totalPages"
                  @click="currentPage = totalPages"
                  class="page-btn"
                >
                  Posledná
                </button>
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
import { articleApi, articleHelpers } from '@/services/article'
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
      // Data
      articles: [],
      conferenceYears: [],
      
      // Loading states
      isLoading: false,
      error: null,
      
      // Filters
      searchQuery: '',
      selectedConferenceYear: '',
      
      // Pagination
      currentPage: 1,
      articlesPerPage: 12
    }
  },
  
  computed: {
    filteredArticles() {
      return articleHelpers.filterArticles(this.articles, {
        search: this.searchQuery,
        conferenceYearId: this.selectedConferenceYear ? parseInt(this.selectedConferenceYear) : undefined
      })
    },
    
    totalArticles() {
      return this.articles.length
    },
    
    uniqueAuthors() {
      const authors = new Set(this.articles.map(article => article.author_name))
      return authors.size
    },
    
    hasActiveFilters() {
      return this.searchQuery || this.selectedConferenceYear
    },
    
    paginatedArticles() {
      const start = (this.currentPage - 1) * this.articlesPerPage
      const end = start + this.articlesPerPage
      return this.filteredArticles.slice(start, end)
    },
    
    totalPages() {
      return Math.ceil(this.filteredArticles.length / this.articlesPerPage)
    }
  },

  watch: {
    filteredArticles() {
      // Reset to first page when filters change
      this.currentPage = 1
    }
  },
  
  mounted() {
    this.loadData()
  },
  
  methods: {
    async loadData() {
      this.isLoading = true
      this.error = null
      
      try {
        // Load articles and conference years in parallel
        const [articlesData, conferenceYearsData] = await Promise.all([
          articleApi.getArticles(),
          conferenceYearApi.getConferenceYears()
        ])
        
        this.articles = articlesData
        this.conferenceYears = conferenceYearsData.data?.data || []
        
      } catch (error) {
        console.error('Error loading publications data:', error)
        this.error = 'Nepodarilo sa načítať publikácie. Skúste to prosím neskôr.'
      } finally {
        this.isLoading = false
      }
    },
    
    formatDate(dateString) {
      return articleHelpers.formatDate(dateString)
    },
    
    getArticlePreview(article) {
      return articleHelpers.formatArticleSummary(article)
    },
    
    getConferenceYearName(yearId) {
      const year = this.conferenceYears.find(y => y.id === parseInt(yearId))
      return year ? `${year.semester} ${year.year}` : ''
    },
    
    clearSearch() {
      this.searchQuery = ''
    },
    
    clearAllFilters() {
      this.searchQuery = ''
      this.selectedConferenceYear = ''
    }
  }
}
</script>

<style scoped>
/* Vaše existujúce CSS štýly zostávajú nezmenené */
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
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 6rem 0 4rem;
  overflow: hidden;
  color: white;
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
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: white;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.title-line {
  display: block;
}

.title-line.highlight {
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 1.25rem;
  margin-bottom: 3rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
  opacity: 0.9;
}

.hero-stats {
  display: flex;
  justify-content: center;
  gap: 4rem;
  margin-top: 3rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  display: block;
  font-size: 2.5rem;
  font-weight: 700;
  color: #fbbf24;
}

.stat-label {
  display: block;
  font-size: 0.875rem;
  opacity: 0.8;
  margin-top: 0.5rem;
}

/* Filter Section */
.filter-section {
  background: var(--bg-secondary);
  padding: 2rem 0;
  border-bottom: 1px solid var(--border-color);
}

.filter-wrapper {
  display: flex;
  gap: 2rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-container {
  flex: 1;
  min-width: 300px;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 1rem;
  color: var(--text-light);
  z-index: 1;
}

.filter-section .search-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 3rem;
  border: 1px solid black;
  border-radius: 25px;
  font-size: 1rem;
  background: white;
  transition: all 0.3s ease;
}

.filter-section .search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.clear-button {
  position: absolute;
  right: 1rem;
  background: none;
  border: none;
  color: var(--text-light);
  cursor: pointer;
  font-size: 1.25rem;
  padding: 0.25rem;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.clear-button:hover {
  color: var(--text-primary);
  background: var(--bg-secondary);
}

.filter-controls {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: 500;
  color: var(--text-secondary);
  font-size: 0.875rem;
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 1px solid black;
  border-radius: 25px;
  background: white;
  font-size: 0.875rem;
  min-width: 180px;
  cursor: pointer;
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
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

.filter-label {
  font-weight: 500;
  color: var(--text-secondary);
  font-size: 0.875rem;
}

.filter-tags {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.filter-tag {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--primary-color);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
}

.remove-filter {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  font-size: 1rem;
  padding: 0;
  margin-left: 0.25rem;
  border-radius: 50%;
  width: 1.25rem;
  height: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s ease;
}

.remove-filter:hover {
  background: rgba(255, 255, 255, 0.2);
}

.clear-all-filters {
  background: none;
  border: 1px solid var(--text-light);
  color: var(--text-secondary);
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.clear-all-filters:hover {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

/* Publications Content */
.publications-content {
  padding: 3rem 0;
}

.results-summary h2 {
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
  width: 3rem;
  height: 3rem;
  border: 3px solid var(--border-color);
  border-top: 3px solid var(--primary-color);
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
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.retry-button,
.clear-filters-button {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  margin-top: 1rem;
  transition: background-color 0.3s ease;
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
}

.publication-card {
  background: white;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
  overflow: hidden;
  transition: all 0.3s ease;
}

.publication-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  background: var(--bg-secondary);
}

.type-badge {
  background: var(--primary-color);
  color: black;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.publication-date {
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
}

.card-body {
  padding: 1.5rem;
  border: 1px solid black;
  border-radius: 25px;
}

.publication-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
  line-height: 1.4;
}

.publication-author {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 1rem;
}

.author-icon {
  font-size: 1rem;
}

.publication-date-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 1rem;
}

.date-icon {
  font-size: 1rem;
}

.publication-meta {
  margin-bottom: 1rem;
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
  font-size: 0.9rem;
  line-height: 1.5;
}

.card-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--border-color);
  background: var(--bg-secondary);
}

.read-more-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
  font-size: 0.875rem;
  transition: all 0.3s ease;
}

.read-more-btn:hover {
  color: var(--primary-dark);
}

.arrow {
  transition: transform 0.3s ease;
}

.read-more-btn:hover .arrow {
  transform: translateX(3px);
}

/* Pagination */
.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 3rem;
}

.pagination {
  background: white;
  border-radius: 8px;
  box-shadow: var(--shadow-sm);
  padding: 1rem;
}

.page-numbers {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.page-btn {
  background: white;
  border: 1px solid var(--border-color);
  color: var(--text-primary);
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.page-btn:hover:not(:disabled) {
  background: var(--bg-secondary);
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.page-btn.active {
  background: var(--primary-color);
  border-color: var(--primary-color);
  color: white;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-info {
  color: var(--text-secondary);
  font-size: 0.875rem;
  padding: 0.5rem 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 2rem;
  }
  
  .filter-wrapper {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-controls {
    justify-content: stretch;
  }
  
  .filter-group {
    flex: 1;
  }
  
  .publications-grid {
    grid-template-columns: 1fr;
  }
  
  .active-filters {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }
  
  .filter-tags {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .search-container {
    min-width: auto;
  }
  
  .page-numbers {
    flex-wrap: wrap;
    justify-content: center;
  }
}
</style>