<template>
  <div class="page-container">
    <!-- Horná lišta -->
    <div class="topbar">
      <div class="topbar-container">
        <div class="topbar-right">
          <button @click="toggleSearch" class="topbar-button">
            <span class="icon-search">🔍</span>
            <span class="button-text">Vyhľadať</span>
          </button>
          
          <button @click="toggleQuicklinks" class="topbar-button">
            <span class="icon-quicklinks">≡</span>
            <span class="button-text">Rýchle odkazy</span>
          </button>
          
          <a href="/login" class="topbar-button">
            <span class="icon-login">👤</span>
            <span class="button-text">Prihlásenie</span>
          </a>
        </div>
      </div>
      
      <!-- Dropdown vyhľadávania -->
      <transition name="slide-down">
        <div class="search-dropdown" v-if="searchOpen">
          <form class="search-form">
            <input type="text" placeholder="Vyhľadajte zamestnancov a obsah" class="search-input">
            <button type="submit" class="search-submit">
              <span>Hľadať</span>
              <i class="search-icon">→</i>
            </button>
          </form>
        </div>
      </transition>
      
      <!-- Dropdown rýchlych odkazov -->
      <transition name="slide-down">
        <div class="quicklinks-dropdown" v-if="quicklinksOpen">
          <div class="quicklinks-grid">
            <div class="quicklinks-column">
              <h3>Odkazy</h3>
              <ul>
                <li><a href="#">Výskumné projekty</a></li>
                <li><a href="#">Materiály fakulty</a></li>
                <li><a href="#">Portál študentov</a></li>
              </ul>
            </div>
            <div class="quicklinks-column">
              <h3>Zdroje</h3>
              <ul>
                <li><a href="#">Knižnica</a></li>
                <li><a href="#">Online výučba</a></li>
                <li><a href="#">Databázy</a></li>
                <li><a href="#">Nástroje pre výskum</a></li>
              </ul>
            </div>
            <div class="quicklinks-column">
              <h3>Kontakt</h3>
              <ul>
                <li><a href="#">Adresár fakulty</a></li>
                <li><a href="#">Kancelária oddelenia</a></li>
                <li><a href="#">Mapa areálu</a></li>
                <li><a href="#">Podpora</a></li>
              </ul>
            </div>
          </div>
          <div class="social-media">
            <a href="#" class="social-icon facebook">📘</a>
            <a href="#" class="social-icon twitter">🐦</a>
            <a href="#" class="social-icon instagram">📷</a>
            <a href="#" class="social-icon linkedin">💼</a>
          </div>
        </div>
      </transition>
    </div>
    
    <!-- Hlavné menu -->
    <nav class="main-navbar">
      <div class="navbar-container">
        <div class="logo-container">
          <a href="/" class="logo-link">
            <div class="logo-wrapper">
              <img src="/src/assets/logo.png" alt="Logo výskumného inštitútu" class="logo">
              <div class="logo-text">
                <span class="institute-name">Výskumný inštitút</span>
                <span class="institute-subtitle">Excelencia vo vede</span>
              </div>
            </div>
          </a>
        </div>
        
        <div class="main-nav-links">
          <a href="/publications" class="main-nav-link active">
            <span>Publikácie</span>
            <div class="nav-underline"></div>
          </a>
          <a href="/departments" class="main-nav-link">
            <span>Oddelenia</span>
            <div class="nav-underline"></div>
          </a>
          <a href="/about" class="main-nav-link">
            <span>O nás</span>
            <div class="nav-underline"></div>
          </a>
        </div>
      </div>
    </nav>

    <!-- Hlavný obsah -->
    <main class="main-content">
      <!-- Hero sekcia -->
      <section class="hero-section">
        <div class="hero-background">
          <div class="hero-overlay"></div>
          <div class="hero-particles"></div>
        </div>
        <div class="hero-content">
          <div class="hero-text">
            <h1 class="hero-title">
              <span class="title-line">Vedecké</span>
              <span class="title-line highlight">Publikácie</span>
            </h1>
            <p class="hero-subtitle">
              Preskúmajte naše najnovšie výskumne práce a vedecké príspevky
            </p>
          </div>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">{{ totalPublications }}+</span>
              <span class="stat-label">Publikácií</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ totalAuthors }}+</span>
              <span class="stat-label">Autorov</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ conferenceYears.length }}+</span>
              <span class="stat-label">Ročníkov</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Filter sekcia -->
      <section class="filter-section">
        <div class="container">
          <div class="filter-wrapper">
            <div class="search-container">
              <div class="search-input-wrapper">
                <span class="search-icon">🔍</span>
                <input 
                  type="text" 
                  v-model="searchQuery" 
                  placeholder="Vyhľadajte publikácie podľa názvu alebo autora..."
                  class="search-input"
                  @input="performSearch"
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
            
            <div class="filter-controls">
              <div class="filter-group">
                <label for="conferenceYear">Ročník konferencie</label>
                <select 
                  id="conferenceYear" 
                  v-model="selectedConferenceYear" 
                  class="filter-select"
                  @change="applyFilters"
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
              
              <div class="filter-group">
                <label for="sortBy">Zoradiť podľa</label>
                <select 
                  id="sortBy" 
                  v-model="sortBy" 
                  class="filter-select"
                  @change="applySort"
                >
                  <option value="created_at">Najnovšie</option>
                  <option value="title">Názov (A-Z)</option>
                  <option value="author_name">Autor (A-Z)</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Aktívne filtre -->
          <div v-if="hasActiveFilters" class="active-filters">
            <span class="filter-label">Aktívne filtre:</span>
            <div class="filter-tags">
              <span v-if="selectedConferenceYear" class="filter-tag">
                {{ getConferenceYearName(selectedConferenceYear) }}
                <button @click="clearConferenceYearFilter" class="remove-filter">×</button>
              </span>
              <span v-if="searchQuery" class="filter-tag">
                Hľadanie: "{{ searchQuery }}"
                <button @click="clearSearch" class="remove-filter">×</button>
              </span>
            </div>
            <button @click="clearAllFilters" class="clear-all-filters">
              Vymazať všetky filtre
            </button>
          </div>
        </div>
      </section>

      <!-- Publikácie obsah -->
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
              <article 
                v-for="article in paginatedArticles" 
                :key="article.id" 
                class="publication-card"
              >
                <div class="card-header">
                  <span class="type-badge">Vedecký článok</span>
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
                    <div class="conference-info">
                      <span class="conference-icon">🎓</span>
                      <span>
                        {{ article.conference_year ? 
                           `${article.conference_year.semester} ${article.conference_year.year}` : 
                           'Nezadaný ročník' 
                        }}
                      </span>
                    </div>
                  </div>
                  
                  <div v-if="article.content" class="publication-abstract">
                    {{ truncateContent(article.content, 150) }}
                  </div>
                </div>
                
                <div class="card-footer">
                  <button 
                    @click="openArticle(article)" 
                    class="read-more-btn"
                  >
                    Čítať viac
                    <span class="arrow">→</span>
                  </button>
                </div>
              </article>
            </div>

            <!-- Paginácia -->
            <div v-if="totalPages > 1" class="pagination-wrapper">
              <div class="pagination">
                <div class="page-numbers">
                  <button 
                    @click="goToPage(currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="page-btn"
                  >
                    ‹ Predošlá
                  </button>
                  
                  <button 
                    v-for="page in visiblePages" 
                    :key="page"
                    @click="goToPage(page)"
                    :class="['page-btn', { active: page === currentPage }]"
                  >
                    {{ page }}
                  </button>
                  
                  <button 
                    @click="goToPage(currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="page-btn"
                  >
                    Ďalšia ›
                  </button>
                </div>
                
                <div class="pagination-info">
                  Strana {{ currentPage }} z {{ totalPages }} 
                  ({{ filteredArticles.length }} publikácií)
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Article Reader Modal -->
      <div v-if="selectedArticle" class="article-reader-modal">
        <div class="modal-overlay" @click="closeArticle"></div>
        <div class="modal-container">
          <div class="modal-header">
            <h2>{{ selectedArticle.title }}</h2>
            <button @click="closeArticle" class="close-button">×</button>
          </div>
          
          <div class="modal-content">
            <div class="article-header">
              <h1>{{ selectedArticle.title }}</h1>
              
              <div class="article-meta">
                <div class="meta-item">
                  <span class="meta-label">Autor:</span>
                  <span class="meta-value">{{ selectedArticle.author_name }}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Ročník konferencie:</span>
                  <span class="meta-value">
                    {{ selectedArticle.conference_year ? 
                       `${selectedArticle.conference_year.semester} ${selectedArticle.conference_year.year}` : 
                       'Nezadaný ročník' 
                    }}
                  </span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Publikované:</span>
                  <span class="meta-value">{{ formatDate(selectedArticle.created_at) }}</span>
                </div>
              </div>
            </div>
            
            <div class="article-content">
              <div v-if="selectedArticle.content" class="content-body" v-html="selectedArticle.content"></div>
              <div v-else class="no-content">
                <p>Obsah tohto článku nie je k dispozícii.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="container">
          <div class="footer-grid">
            <div class="footer-column main">
              <div class="footer-logo">
                <h3>Výskumný inštitút</h3>
                <p class="footer-tagline">Excelencia vo vede & inováciách</p>
              </div>
              <div class="contact-info">
                <div class="contact-item">
                  <span class="contact-icon">📍</span>
                  <span>Univerzitný kampus, 1180 Viedeň, Rakúsko</span>
                </div>
                <div class="contact-item">
                  <span class="contact-icon">📞</span>
                  <span>+43 1 47654 0</span>
                </div>
                <div class="contact-item">
                  <span class="contact-icon">✉️</span>
                  <span>research@institute.ac.at</span>
                </div>
              </div>
            </div>
            <div class="footer-column">
              <h4>Rýchle odkazy</h4>
              <ul>
                <li><a href="/publications">Publikácie</a></li>
                <li><a href="/departments">Oddelenia</a></li>
                <li><a href="/about">O nás</a></li>
                <li><a href="/contact">Kontakt</a></li>
              </ul>
            </div>
            <div class="footer-column">
              <h4>Kontaktujte nás</h4>
              <div class="social-links">
                <a href="#" class="social-link facebook">📘 Facebook</a>
                <a href="#" class="social-link twitter">🐦 Twitter</a>
                <a href="#" class="social-link linkedin">💼 LinkedIn</a>
                <a href="#" class="social-link youtube">📺 YouTube</a>
              </div>
              <div class="newsletter">
                <h5>Newsletter</h5>
                <p>Buďte informovaní o našich najnovších výskumoch</p>
                <div class="newsletter-form">
                  <input type="email" placeholder="Váš email">
                  <button>Prihlásiť sa</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="container">
          <p>© 2025 Výskumný inštitút. Všetky práva vyhradené.</p>
          <div class="footer-links">
            <a href="/privacy">Zásady ochrany osobných údajov</a>
            <a href="/terms">Podmienky používania</a>
            <a href="/sitemap">Mapa stránok</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { articleApi } from '@/services/article'
import { conferenceYearApi, conferenceYearHelpers } from '@/services/conferenceYear'

export default {
  name: 'PublicationsView',
  
  data() {
    return {
      searchOpen: false,
      quicklinksOpen: false,
      loading: false,
      error: null,
      articles: [],
      conferenceYears: [],
      searchQuery: '',
      selectedConferenceYear: '',
      sortBy: 'created_at',
      selectedArticle: null,
      currentPage: 1,
      itemsPerPage: 12
    }
  },
  
  computed: {
    totalPublications() {
      return this.articles.length
    },
    totalAuthors() {
      const authors = new Set(this.articles.map(article => article.author_name))
      return authors.size
    },
    filteredArticles() {
      let filtered = [...this.articles]
      
      // Search filter
      if (this.searchQuery.trim()) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(article => 
          article.title.toLowerCase().includes(query) ||
          article.author_name.toLowerCase().includes(query)
        )
      }
      
      // Conference year filter
      if (this.selectedConferenceYear) {
        filtered = filtered.filter(article => 
          article.conference_year_id == this.selectedConferenceYear
        )
      }
      
      // Sort
      filtered.sort((a, b) => {
        switch (this.sortBy) {
          case 'title':
            return a.title.localeCompare(b.title)
          case 'author_name':
            return a.author_name.localeCompare(b.author_name)
          case 'created_at':
          default:
            return new Date(b.created_at) - new Date(a.created_at)
        }
      })
      
      return filtered
    },
    paginatedArticles() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredArticles.slice(start, end)
    },
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
    hasActiveFilters() {
      return this.searchQuery.trim() || this.selectedConferenceYear
    }
  },
  
  mounted() {
    this.loadData()
  },
  
  methods: {
    toggleSearch() {
      this.searchOpen = !this.searchOpen
      if (this.searchOpen) this.quicklinksOpen = false
    },
    toggleQuicklinks() {
      this.quicklinksOpen = !this.quicklinksOpen
      if (this.quicklinksOpen) this.searchOpen = false
    },
    async loadData() {
      this.loading = true
      this.error = null
      
      try {
        const [articlesData, conferenceYearsData] = await Promise.all([
          articleApi.getArticles(),
          conferenceYearApi.getConferenceYears()
        ])
        
        this.articles = articlesData
        this.conferenceYears = conferenceYearsData
      } catch (error) {
        console.error('Error loading data:', error)
        this.error = 'Chyba pri načítavaní dát. Skúste to znovu.'
      } finally {
        this.loading = false
      }
    },
    performSearch() {
      this.currentPage = 1 // Reset to first page when searching
    },
    clearSearch() {
      this.searchQuery = ''
      this.currentPage = 1
    },
    applyFilters() {
      this.currentPage = 1
    },
    applySort() {
      this.currentPage = 1
    },
    clearConferenceYearFilter() {
      this.selectedConferenceYear = ''
      this.currentPage = 1
    },
    clearAllFilters() {
      this.searchQuery = ''
      this.selectedConferenceYear = ''
      this.currentPage = 1
    },
    getConferenceYearName(yearId) {
      const year = this.conferenceYears.find(y => y.id == yearId)
      return year ? `${year.semester} ${year.year}` : 'Neznámy ročník'
    },
    formatDate(dateString) {
      if (!dateString) return 'Nezadané'
      const date = new Date(dateString)
      return date.toLocaleDateString('sk-SK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    truncateContent(content, maxLength) {
      if (!content) return ''
      const stripped = content.replace(/<[^>]*>/g, '') // Remove HTML tags
      return stripped.length > maxLength 
        ? stripped.substring(0, maxLength) + '...'
        : stripped
    },
    openArticle(article) {
      this.selectedArticle = article
      document.body.style.overflow = 'hidden' // Prevent background scroll
    },
    closeArticle() {
      this.selectedArticle = null
      document.body.style.overflow = 'auto' // Restore scroll
    },
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page
        // Scroll to top of publications section
        this.$nextTick(() => {
          const element = document.querySelector('.publications-content')
          if (element) {
            element.scrollIntoView({ behavior: 'smooth' })
          }
        })
      }
    }
  }
}
</script>

<style scoped>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

/* CSS Variables - rovnaké ako HomeView */
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

/* Base Styles - rovnaké ako HomeView */
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

/* Transitions */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Topbar - rovnaké ako HomeView */
.topbar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
}

.topbar-container {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 0.75rem 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.topbar-button {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  padding: 0.5rem 1rem;
  color: white;
  font-size: 0.875rem;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.topbar-button:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-1px);
}

.icon-search, .icon-quicklinks, .icon-login {
  margin-right: 0.5rem;
  font-size: 1rem;
}

/* Search & Quicklinks Dropdowns - rovnaké ako HomeView */
.search-dropdown, .quicklinks-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border-color);
  box-shadow: var(--shadow-lg);
  z-index: 100;
}

.search-dropdown {
  padding: 1.5rem;
}

.search-form {
  display: flex;
  max-width: 600px;
  margin: 0 auto;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

.search-input {
  flex: 1;
  padding: 1rem 1.5rem;
  border: none;
  font-size: 1rem;
  outline: none;
  background: white;
}

.search-submit {
  background: var(--gradient-primary);
  color: white;
  border: none;
  padding: 0 2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.search-submit:hover {
  transform: scale(1.05);
}

.quicklinks-dropdown {
  padding: 2rem;
}

.quicklinks-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 3rem;
  max-width: 1000px;
  margin: 0 auto 2rem;
}

.quicklinks-column h3 {
  font-size: 1.25rem;
  margin-bottom: 1rem;
  color: var(--text-primary);
  font-weight: 600;
}

.quicklinks-column ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.quicklinks-column li {
  margin-bottom: 0.75rem;
}

.quicklinks-column a {
  color: var(--text-secondary);
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-block;
}

.quicklinks-column a:hover {
  color: var(--primary-color);
  transform: translateX(5px);
}

.social-media {
  display: flex;
  justify-content: center;
  gap: 1rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
}

.social-icon {
  padding: 0.75rem;
  border-radius: 12px;
  background: var(--bg-secondary);
  text-decoration: none;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.social-icon:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
}

.social-icon.facebook:hover { background: #1877f2; color: white; }
.social-icon.twitter:hover { background: #1da1f2; color: white; }
.social-icon.instagram:hover { background: #e4405f; color: white; }
.social-icon.linkedin:hover { background: #0077b5; color: white; }

/* Main Navbar - rovnaké ako HomeView */
.main-navbar {
  background: white;
  box-shadow: var(--shadow-md);
  position: sticky;
  top: 0;
  z-index: 50;
}

.navbar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}

.logo-container {
  flex: 0 0 auto;
}

.logo-link {
  text-decoration: none;
  color: inherit;
}

.logo-wrapper {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  max-height: 50px;
  width: auto;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.institute-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  font-family: 'Poppins', sans-serif;
}

.institute-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  font-weight: 400;
}

.main-nav-links {
  display: flex;
  gap: 2rem;
}

.main-nav-link {
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
  font-size: 1.1rem;
  position: relative;
  padding: 0.5rem 0;
  transition: color 0.3s ease;
}

.main-nav-link:hover,
.main-nav-link.active {
  color: var(--primary-color);
}

.nav-underline {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: var(--gradient-primary);
  transition: width 0.3s ease;
}

.main-nav-link:hover .nav-underline,
.main-nav-link.active .nav-underline {
  width: 100%;
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
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(4px);
}

.modal-container {
  position: relative;
  background: white;
  border-radius: 16px;
  max-width: 800px;
  max-height: 90vh;
  width: 100%;
  overflow: hidden;
  box-shadow: var(--shadow-xl);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--border-color);
  background: var(--bg-secondary);
}

.modal-header h2 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.4;
}

.close-button {
  background: none;
  border: none;
  font-size: 2rem;
  cursor: pointer;
  color: var(--text-light);
  transition: color 0.3s ease;
  padding: 0;
  line-height: 1;
}

.close-button:hover {
  color: var(--text-primary);
}

.modal-content {
  overflow-y: auto;
  max-height: calc(90vh - 80px);
}

.article-header {
  padding: 2rem;
  border-bottom: 1px solid var(--border-color);
}

.article-header h1 {
  font-family: 'Poppins', sans-serif;
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  line-height: 1.3;
}

.article-meta {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.meta-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.meta-label {
  font-weight: 600;
  color: var(--text-secondary);
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.meta-value {
  color: var(--text-primary);
  font-weight: 500;
}

.article-content {
  padding: 2rem;
}

.content-body {
  line-height: 1.8;
  color: var(--text-primary);
}

.content-body h1,
.content-body h2,
.content-body h3 {
  font-family: 'Poppins', sans-serif;
  margin-top: 2rem;
  margin-bottom: 1rem;
  color: var(--text-primary);
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
  padding: 2rem;
  color: var(--text-light);
}

/* Footer - rovnaké ako HomeView */
.footer {
  background: var(--bg-dark);
  color: white;
  margin-top: auto;
}

.footer-content {
  padding: 3rem 0;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 3rem;
}

.footer-column.main {
  padding-right: 2rem;
}

.footer-logo h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  margin-bottom: 0.5rem;
}

.footer-tagline {
  color: #9ca3af;
  margin-bottom: 2rem;
  font-size: 0.9rem;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #d1d5db;
  font-size: 0.9rem;
}

.contact-icon {
  font-size: 1rem;
}

.footer-column h4 {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: white;
}

.footer-column ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-column li {
  margin-bottom: 0.5rem;
}

.footer-column a {
  color: #9ca3af;
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 0.9rem;
}

.footer-column a:hover {
  color: white;
}

.social-links {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.social-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #9ca3af;
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 0.9rem;
}

.social-link:hover {
  color: white;
}

.newsletter h5 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: white;
}

.newsletter p {
  color: #9ca3af;
  font-size: 0.85rem;
  margin-bottom: 1rem;
}

.newsletter-form {
  display: flex;
  gap: 0.5rem;
}

.newsletter-form input {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #374151;
  border-radius: 8px;
  background: #374151;
  color: white;
  font-size: 0.85rem;
}

.newsletter-form input::placeholder {
  color: #9ca3af;
}

.newsletter-form button {
  padding: 0.5rem 1rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.85rem;
  transition: background 0.3s ease;
}

.newsletter-form button:hover {
  background: var(--primary-dark);
}

.footer-bottom {
  background: #111827;
  padding: 1.5rem 0;
}

.footer-bottom .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.footer-bottom p {
  margin: 0;
  color: #9ca3af;
  font-size: 0.85rem;
}

.footer-links {
  display: flex;
  gap: 1.5rem;
}

.footer-links a {
  color: #9ca3af;
  text-decoration: none;
  font-size: 0.85rem;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: white;
}

/* Animations */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .topbar-container {
    padding: 0.5rem 1rem;
  }
  
  .navbar-container {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }
  
  .main-nav-links {
    gap: 1rem;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 2rem;
  }
  
  .filter-wrapper {
    gap: 1.5rem;
  }
  
  .filter-controls {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }
  
  .publications-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .pagination {
    flex-direction: column;
    gap: 1rem;
  }
  
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .footer-bottom .container {
    flex-direction: column;
    text-align: center;
  }
  
  .quicklinks-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .modal-container {
    margin: 1rem;
    max-height: calc(100vh - 2rem);
  }
  
  .article-header h1 {
    font-size: 1.5rem;
  }
  
  .article-meta {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .publications-grid {
    gap: 1rem;
  }
  
  .publication-card {
    padding: 1rem;
  }
  
  .page-numbers {
    gap: 0.25rem;
  }
  
  .page-btn {
    padding: 0.4rem 0.6rem;
    font-size: 0.8rem;
    min-width: 36px;
  }
}
</style>