<template>
  <div class="publications-page">
    <!-- Add Navbar -->
    <navbar-component />
    
    <!-- Publications Hero Section -->
    <section class="publications-hero">
      <div class="hero-background"></div>
      <div class="hero-overlay"></div>
      <div class="hero-particles"></div>
      
      <div class="hero-content">
        <div class="container">
          <h1 class="hero-title">
            <span class="title-line">Research</span>
            <span class="title-line highlight">Publications</span>
          </h1>
          <p class="hero-subtitle">
            Discover cutting-edge research and academic publications from our institute
          </p>
          
          <div class="hero-stats" v-if="!loading">
            <div class="stat-item">
              <div class="stat-number">{{ totalArticles }}</div>
              <div class="stat-label">Total Publications</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">{{ conferenceYears.length }}</div>
              <div class="stat-label">Conference Years</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">{{ uniqueAuthors }}</div>
              <div class="stat-label">Authors</div>
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
                type="text"
                v-model="searchQuery"
                @input="handleSearch"
                placeholder="Search publications..."
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
              <label for="year-filter">Conference Year</label>
              <select
                id="year-filter"
                v-model="selectedYear"
                @change="handleYearFilter"
                class="filter-select"
              >
                <option value="">All Years</option>
                <option 
                  v-for="year in conferenceYears" 
                  :key="year.id" 
                  :value="year.id"
                >
                  {{ conferenceYearHelpers.formatConferenceYear(year) }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Active Filters -->
        <div class="active-filters" v-if="hasActiveFilters">
          <span class="filter-label">Active filters:</span>
          <div class="filter-tags">
            <span v-if="searchQuery" class="filter-tag">
              Search: "{{ searchQuery }}"
              <button @click="clearSearch" class="remove-filter">✕</button>
            </span>
            <span v-if="selectedYear" class="filter-tag">
              Year: {{ getSelectedYearName() }}
              <button @click="clearYearFilter" class="remove-filter">✕</button>
            </span>
          </div>
          <button @click="clearAllFilters" class="clear-all-filters">
            Clear all filters
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
            {{ filteredArticles.length === 1 ? 'Publication' : 'Publications' }}
            {{ hasActiveFilters ? 'found' : 'available' }}
          </h2>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading publications...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-state">
          <div class="error-icon">⚠️</div>
          <h3>Error Loading Publications</h3>
          <p>{{ error }}</p>
          <button @click="loadData" class="retry-button">Try Again</button>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredArticles.length === 0" class="empty-state">
          <div class="empty-icon">📄</div>
          <h3>No Publications Found</h3>
          <p v-if="hasActiveFilters">
            Try adjusting your search criteria or clearing filters.
          </p>
          <p v-else>
            No publications are currently available.
          </p>
          <button 
            v-if="hasActiveFilters" 
            @click="clearAllFilters" 
            class="clear-filters-button"
          >
            Clear Filters
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
              <span class="type-badge">Article</span>
              <span class="publication-date">
                {{ formatDate(article.created_at) }}
              </span>
            </div>
            
            <div class="card-body">
              <h3 class="publication-title">{{ article.title }}</h3>
              
              <div class="publication-author">
                <span class="author-icon">👤</span>
                {{ article.author_name }}
              </div>
              
              <div class="publication-meta">
                <div class="conference-info" v-if="article.conference_year">
                  <span class="conference-icon">📅</span>
                  {{ conferenceYearHelpers.formatConferenceYear(article.conference_year) }}
                </div>
              </div>
              
              <div class="publication-abstract" v-if="article.content">
                {{ truncateText(article.content, 150) }}
              </div>
            </div>
            
            <div class="card-footer">
              <button 
                @click="openArticleReader(article)" 
                class="read-more-btn"
              >
                Read Full Article
                <span class="arrow">→</span>
              </button>
              
              <div class="card-actions">
                <button class="action-btn" title="Share">📤</button>
                <button class="action-btn" title="Cite">📎</button>
              </div>
            </div>
          </article>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="pagination-wrapper">
          <div class="pagination">
            <button 
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="page-btn"
            >
              ← Previous
            </button>
            
            
            <button 
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="page-btn"
            >
              Next →
            </button>
          </div>
          
          <div class="pagination-info">
            Showing {{ startIndex + 1 }}-{{ endIndex }} of {{ filteredArticles.length }}
          </div>
        </div>
      </div>
    </section>

    <!-- Article Reader Modal -->
    <div v-if="selectedArticle" class="article-reader-modal">
      <div class="modal-overlay" @click="closeArticleReader"></div>
      <div class="modal-container">
        <div class="modal-header">
          <h2>{{ selectedArticle.title }}</h2>
          <button @click="closeArticleReader" class="close-button">✕</button>
        </div>
        <div class="modal-content">
          <div class="article-header">
            <h1>{{ selectedArticle.title }}</h1>
            <div class="article-meta">
              <div class="meta-item">
                <span class="meta-label">Author:</span>
                <span class="meta-value">{{ selectedArticle.author_name }}</span>
              </div>
              <div class="meta-item" v-if="selectedArticle.conference_year">
                <span class="meta-label">Conference Year:</span>
                <span class="meta-value">
                  {{ conferenceYearHelpers.formatConferenceYear(selectedArticle.conference_year) }}
                </span>
              </div>
              <div class="meta-item">
                <span class="meta-label">Published:</span>
                <span class="meta-value">{{ formatDate(selectedArticle.created_at) }}</span>
              </div>
            </div>
          </div>
          <div class="article-content">
            <div class="content-body" v-if="selectedArticle.content">
              <div v-html="selectedArticle.content"></div>
            </div>
            <div v-else class="no-content">
              <p>No content available for this article.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Footer -->
    <footer-component />
  </div>
</template>

<script>
import { articleApi } from '@/services/article'
import { conferenceYearApi, conferenceYearHelpers } from '@/services/conferenceYear'
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
      // Data properties
      articles: [],
      conferenceYears: [],
      loading: true,
      error: null,
      
      // Search and filters
      searchQuery: '',
      selectedYear: '',
      
      // Pagination
      currentPage: 1,
      itemsPerPage: 12,
      
      // Article reader
      selectedArticle: null,
      
      // Helper reference
      conferenceYearHelpers
    }
  },
  
  computed: {
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
          article.conference_year_id === parseInt(this.selectedYear)
        )
      }
      
      return filtered
    },
    
    totalArticles() {
      return this.articles.length
    },
    
    uniqueAuthors() {
      const authors = new Set(this.articles.map(article => article.author_name))
      return authors.size
    },
    
    hasActiveFilters() {
      return this.searchQuery || this.selectedYear
    },
    
    totalPages() {
      return Math.ceil(this.filteredArticles.length / this.itemsPerPage)
    },
    
    paginatedArticles() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredArticles.slice(start, end)
    },
    
    startIndex() {
      return (this.currentPage - 1) * this.itemsPerPage
    },
    
    endIndex() {
      return Math.min(
        this.startIndex + this.itemsPerPage,
        this.filteredArticles.length
      )
    },
    
    visiblePages() {
      const pages = []
      const start = Math.max(1, this.currentPage - 2)
      const end = Math.min(this.totalPages, this.currentPage + 2)
      
      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      
      return pages
    }
  },
  
  mounted() {
    this.loadData()
  },
  
  methods: {
    async loadData() {
      this.loading = true
      this.error = null
      
      try {
        // Load articles and conference years in parallel
        const [articlesResult, yearsResult] = await Promise.all([
          articleApi.getArticles(),
          conferenceYearApi.getConferenceYears()
        ])
        
        this.articles = articlesResult || []
        this.conferenceYears = yearsResult || []
      } catch (error) {
        console.error('Error loading publications data:', error)
        this.error = 'Failed to load publications. Please try again later.'
      } finally {
        this.loading = false
      }
    },
    
    handleSearch() {
      this.currentPage = 1 // Reset to first page when searching
    },
    
    handleYearFilter() {
      this.currentPage = 1 // Reset to first page when filtering
    },
    
    clearSearch() {
      this.searchQuery = ''
      this.currentPage = 1
    },
    
    clearYearFilter() {
      this.selectedYear = ''
      this.currentPage = 1
    },
    
    clearAllFilters() {
      this.searchQuery = ''
      this.selectedYear = ''
      this.currentPage = 1
    },
    
    getSelectedYearName() {
      const year = this.conferenceYears.find(y => y.id === parseInt(this.selectedYear))
      return year ? conferenceYearHelpers.formatConferenceYear(year) : ''
    },
    
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page
        // Scroll to top of results
        document.querySelector('.publications-content')?.scrollIntoView({
          behavior: 'smooth'
        })
      }
    },
    
    openArticleReader(article) {
      this.selectedArticle = article
      document.body.style.overflow = 'hidden' // Prevent background scroll
    },
    
    closeArticleReader() {
      this.selectedArticle = null
      document.body.style.overflow = 'auto' // Restore scroll
    },
    
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      
      try {
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        })
      } catch {
        return 'Invalid date'
      }
    },
    
    truncateText(text, maxLength) {
      if (!text) return ''
      if (text.length <= maxLength) return text
      return text.substring(0, maxLength) + '...'
    }
  }
}
</script>

<style scoped>
/* Publications Page Styles */
.publications-page {
  min-height: 100vh;
  background-color: #f8f9fa;
}

/* Hero Section */
.publications-hero {
  position: relative;
  height: 60vh;
  min-height: 400px;
  display: flex;
  align-items: center;
  overflow: hidden;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 40% 80%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.2);
}

.hero-particles {
  position: absolute;
  width: 100%;
  height: 100%;
  background-image: 
    radial-gradient(2px 2px at 20px 30px, rgba(255, 255, 255, 0.3), transparent),
    radial-gradient(2px 2px at 40px 70px, rgba(255, 255, 255, 0.2), transparent),
    radial-gradient(1px 1px at 90px 40px, rgba(255, 255, 255, 0.4), transparent);
  animation: float 20s ease-in-out infinite;
}

.hero-content {
  position: relative;
  z-index: 2;
  color: white;
  text-align: center;
  width: 100%;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
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
  margin-bottom: 2rem;
  opacity: 0.9;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.hero-stats {
  display: flex;
  justify-content: center;
  gap: 3rem;
  margin-top: 2rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 700;
  display: block;
  color: #ffd700;
}

.stat-label {
  font-size: 0.9rem;
  opacity: 0.8;
  margin-top: 0.25rem;
}

/* Filter Section */
.filter-section {
  background: white;
  padding: 2rem 0;
  border-bottom: 1px solid #e9ecef;
}

.filter-wrapper {
  display: flex;
  gap: 2rem;
  align-items: end;
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
  left: 12px;
  z-index: 1;
  color: #6c757d;
}

.search-input {
  width: 100%;
  padding: 12px 16px 12px 40px;
  border: 2px solid #e9ecef;
  border-radius: 25px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: #f8f9fa;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
  background-color: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.clear-button {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
  padding: 4px;
  border-radius: 50%;
}

.filter-controls {
  display: flex;
  gap: 1rem;
  align-items: end;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-size: 0.9rem;
  font-weight: 500;
  color: #495057;
}

.filter-select {
  padding: 10px 16px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 0.95rem;
  background-color: white;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Active Filters */
.active-filters {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.filter-label {
  font-weight: 500;
  color: #495057;
}

.filter-tags {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.filter-tag {
  background-color: #667eea;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.remove-filter {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 0;
  font-size: 0.8rem;
}

.clear-all-filters {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.85rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.clear-all-filters:hover {
  background-color: #c82333;
}

/* Publications Content */
.publications-content {
  padding: 3rem 0;
}

.results-summary h2 {
  font-size: 1.5rem;
  margin-bottom: 2rem;
  color: #333;
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
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

.error-icon,
.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.retry-button,
.clear-filters-button {
  background-color: #667eea;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  margin-top: 1rem;
  transition: background-color 0.3s ease;
}

.retry-button:hover,
.clear-filters-button:hover {
  background-color: #5a67d8;
}

/* Publications Grid */
.publications-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.publication-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid #e9ecef;
}

.publication-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.card-header {
  padding: 1rem 1.5rem;
  background-color: #f8f9fa;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e9ecef;
}

.type-badge {
  background-color: #667eea;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
}

.publication-date {
  color: #6c757d;
  font-size: 0.85rem;
}

.card-body {
  padding: 1.5rem;
}

.publication-title {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #333;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.publication-author {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  color: #495057;
  font-size: 0.9rem;
}

.author-icon {
  color: #6c757d;
}

.publication-meta {
  margin-bottom: 1rem;
}

.conference-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6c757d;
  font-size: 0.85rem;
}

.conference-icon {
  color: #667eea;
}

.publication-abstract {
  color: #6c757d;
  font-size: 0.9rem;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-footer {
  padding: 1rem 1.5rem;
  background-color: #f8f9fa;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #e9ecef;
}

.read-more-btn {
  background-color: #667eea;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.read-more-btn:hover {
  background-color: #5a67d8;
}

.arrow {
  transition: transform 0.3s ease;
}

.read-more-btn:hover .arrow {
  transform: translateX(2px);
}

.card-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  background: none;
  border: 1px solid #dee2e6;
  padding: 0.5rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #6c757d;
}

.action-btn:hover {
  background-color: #e9ecef;
  border-color: #adb5bd;
}

/* Pagination */
.pagination-wrapper {
  margin-top: 3rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.pagination {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.page-numbers {
  display: flex;
  gap: 0.25rem;
}

.page-btn {
  padding: 0.5rem 0.75rem;
  border: 1px solid #dee2e6;
  background: white;
  color: #495057;
  cursor: pointer;
  border-radius: 6px;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.page-btn:hover:not(:disabled) {
  background-color: #e9ecef;
  border-color: #adb5bd;
}

.page-btn.active {
  background-color: #667eea;
  border-color: #667eea;
  color: white;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-info {
  color: #6c757d;
  font-size: 0.9rem;
}

/* Article Reader Modal */
.article-reader-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.8);
}

.modal-container {
  position: relative;
  background: white;
  border-radius: 12px;
  max-width: 800px;
  max-height: 90vh;
  width: 100%;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.modal-header {
  padding: 1.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.3rem;
  color: #333;
  max-width: calc(100% - 40px);
}

.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0;
  color: #6c757d;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.close-button:hover {
  background-color: #e9ecef;
  color: #495057;
}

.modal-content {
  max-height: calc(90vh - 80px);
  overflow-y: auto;
}

.article-header {
  padding: 2rem;
  border-bottom: 1px solid #e9ecef;
}

.article-header h1 {
  margin: 0 0 1.5rem 0;
  font-size: 1.8rem;
  color: #333;
  line-height: 1.3;
}

.article-meta {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.meta-item {
  display: flex;
  gap: 1rem;
}

.meta-label {
  font-weight: 600;
  color: #495057;
  min-width: 120px;
}

.meta-value {
  color: #6c757d;
}

.article-content {
  padding: 2rem;
}

.content-body {
  line-height: 1.7;
  color: #333;
}

.content-body h1,
.content-body h2,
.content-body h3 {
  margin-top: 2rem;
  margin-bottom: 1rem;
  color: #333;
}

.content-body p {
  margin-bottom: 1rem;
}

.content-body ul,
.content-body ol {
  margin-bottom: 1rem;
  padding-left: 2rem;
}

.no-content {
  text-align: center;
  color: #6c757d;
  font-style: italic;
  padding: 2rem;
}

/* Animations */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .filter-wrapper {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-container {
    min-width: auto;
  }
  
  .publications-grid {
    grid-template-columns: 1fr;
  }
  
  .pagination-wrapper {
    flex-direction: column;
    text-align: center;
  }
  
  .article-reader-modal {
    padding: 1rem;
  }
  
  .modal-container {
    max-height: 95vh;
  }
  
  .article-header,
  .article-content {
    padding: 1.5rem;
  }
  
  .meta-item {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .meta-label {
    min-width: auto;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1rem;
  }
  
  .stat-number {
    font-size: 2rem;
  }
  
  .publication-card {
    margin: 0 -0.5rem;
  }
  
  .card-footer {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .card-actions {
    justify-content: center;
  }
}

/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

@media (min-width: 768px) {
  .container {
    padding: 0 2rem;
  }
}
</style>