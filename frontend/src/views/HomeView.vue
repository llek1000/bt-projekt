<template>
  <div class="page-container">
    <NavbarComponent />
    
    <main class="main-content">
      <!-- Hero sekcia -->
      <section class="hero-section">
        <div class="hero-background">
          <div class="hero-overlay"></div>
          <div class="hero-particles">
            <div class="particle" v-for="n in 20" :key="n" 
                 :style="{ left: Math.random() * 100 + '%', top: Math.random() * 100 + '%' }"></div>
          </div>
        </div>
        
        <div class="container">
          <div class="hero-content">
            <h1 class="hero-title">
              <span class="title-line">Výskumný Inštitút</span>
              <span class="title-line highlight">Moderných Technológií</span>
            </h1>
            <p class="hero-subtitle">
              Sme popredná inštitúcia zameraná na výskum a vývoj najnovších technológií. 
              Naša práca formuje budúcnosť vedy a technológií.
            </p>
            <div class="hero-buttons">
              <router-link to="/publications" class="hero-btn primary">
                Naše Publikácie
                <span class="btn-arrow">→</span>
              </router-link>
              <router-link to="/about" class="hero-btn secondary">
                O Nás
                <span class="btn-arrow">→</span>
              </router-link>
            </div>
            
            <div class="hero-stats">
              <div class="stat-item">
                <span class="stat-number">{{ totalArticles }}</span>
                <span class="stat-label">Publikácií</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">4</span>
                <span class="stat-label">Oddelení</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">15+</span>
                <span class="stat-label">Rokov Výskumu</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Najnovšie články -->
      <section class="latest-news">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Najnovšie Články</h2>
            <p class="section-subtitle">
              Objavte naše najnovšie výskumné práce a vedecké publikácie
            </p>
          </div>

          <!-- Loading state -->
          <div v-if="isLoading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Načítavajú sa články...</p>
          </div>

          <!-- Error state -->
          <div v-else-if="error" class="error-state">
            <div class="error-icon">⚠️</div>
            <h3>Chyba pri načítavaní</h3>
            <p>{{ error }}</p>
            <button @click="loadLatestArticles" class="retry-button">
              Skúsiť znovu
            </button>
          </div>

          <!-- Articles grid -->
          <div v-else-if="latestArticles.length > 0" class="news-grid">
            <article 
              v-for="(article, index) in latestArticles" 
              :key="article.id"
              class="news-card"
              :class="{ featured: index === 0 }"
            >
              <div class="news-content">
                <div class="news-meta">
                  <span class="news-category">
                    {{ formatConferenceYear(article.conference_year) }}
                  </span>
                  <span class="news-date">{{ formatDate(article.created_at) }}</span>
                </div>
                <h3>{{ article.title }}</h3>
                <p class="news-author">
                  <span class="author-icon">👤</span>
                  {{ article.author_name }}
                </p>
                <p class="news-excerpt">{{ getArticlePreview(article.content) }}</p>
                <router-link 
                  :to="`/articles/${article.id}`" 
                  class="read-more"
                >
                  Čítať viac
                  <span class="arrow">→</span>
                </router-link>
              </div>
            </article>
          </div>

          <!-- Empty state -->
          <div v-else class="empty-state">
            <div class="empty-icon">📄</div>
            <h3>Žiadne články</h3>
            <p>Momentálne nie sú k dispozícii žiadne články.</p>
          </div>

          <!-- Show more button -->
          <div v-if="latestArticles.length > 0" class="section-footer">
            <router-link to="/publications" class="hero-btn secondary">
              Zobraziť všetky publikácie
              <span class="btn-arrow">→</span>
            </router-link>
          </div>
        </div>
      </section>

      <!-- Vítacia sekcia -->
      <section class="welcome-section">
        <div class="container">
          <div class="welcome-content">
            <div class="welcome-text">
              <h2>Vitajte v našom výskumnom centre</h2>
              <p>
                Sme moderná vedecká inštitúcia zameraná na <span class="highlight-text">inovatívny výskum</span> 
                v oblasti biotechnológií, informatiky a pokročilých materiálov. Naše tímy pracujú na 
                projektoch, ktoré môžu zmeniť svet.
              </p>
            </div>
            
            <div class="welcome-features">
              <div class="feature-item">
                <div class="feature-icon">🔬</div>
                <h4>Moderné Laboratóriá</h4>
                <p>Najnovšie vybavenie pre pokročilý výskum</p>
              </div>
              <div class="feature-item">
                <div class="feature-icon">👥</div>
                <h4>Odborný Tím</h4>
                <p>Skúsení vedci a výskumníci</p>
              </div>
              <div class="feature-item">
                <div class="feature-icon">🌍</div>
                <h4>Globálne Partnerstvá</h4>
                <p>Spolupráca s univerzitami po celom svete</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Výskumné oblasti -->
      <section class="research-areas">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Naše Výskumné Oblasti</h2>
            <p class="section-subtitle">
              Špecializujeme sa na klúčové oblasti modernej vedy a technológií
            </p>
          </div>
          
          <div class="research-grid">
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon life-sciences">🧬</div>
                <h3>Vedy o Živote</h3>
              </div>
              <p>Výskum v oblasti molekulárnej biológie, genetiky a biotechnológií pre zdravie budúcnosti.</p>
              <div class="card-footer">
                <router-link to="/departments" class="learn-more">Zistiť viac →</router-link>
              </div>
            </div>
            
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon data-science">💻</div>
                <h3>Dátová Veda</h3>
              </div>
              <p>Analýza veľkých dát, umelá inteligencia a strojové učenie pre riešenie komplexných problémov.</p>
              <div class="card-footer">
                <router-link to="/departments" class="learn-more">Zistiť viac →</router-link>
              </div>
            </div>
            
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon environmental">🌱</div>
                <h3>Environmentálne Technológie</h3>
              </div>
              <p>Udržateľné riešenia pre ochranu životného prostredia a zelené technológie.</p>
              <div class="card-footer">
                <router-link to="/departments" class="learn-more">Zistiť viac →</router-link>
              </div>
            </div>
            
          </div>
        </div>
      </section>
    </main>

    <FooterComponent />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { articleApi } from '@/services/article'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

export default defineComponent({
  name: 'HomeView',
  components: {
    NavbarComponent,
    FooterComponent
  },
  
  data() {
    return {
      latestArticles: [] as any[],
      isLoading: false,
      error: null as string | null,
      totalArticles: 0
    }
  },
  
  async mounted() {
    await this.loadLatestArticles()
  },
  
  methods: {
    async loadLatestArticles() {
      this.isLoading = true
      this.error = null
      
      try {
        const articles = await articleApi.getArticles()
        
        // Get latest 6 articles
        this.latestArticles = articles.slice(0, 5)
        this.totalArticles = articles.length
        
      } catch (error) {
        console.error('Error loading articles:', error)
        this.error = 'Nepodarilo sa načítať články'
      } finally {
        this.isLoading = false
      }
    },
    
    formatDate(dateString?: string): string {
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
    
    formatConferenceYear(conferenceYear?: any): string {
      if (!conferenceYear) return 'Neznámy ročník'
      return `${conferenceYear.semester} ${conferenceYear.year}`
    },
    
    getArticlePreview(content?: string): string {
      if (!content) return 'Žiadny obsah...'
      
      const plainText = content.replace(/<[^>]*>/g, '')
      return plainText.length > 150 
        ? plainText.substring(0, 150) + '...' 
        : plainText
    }
  }
})
</script>

<style scoped>
/* Základné štýly */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.page-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: 'Inter', sans-serif;
  color: #1f2937;
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

/* Animácie */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Prechody */
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

/* Horná lišta */
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

/* Vyhľadávanie & Rýchle odkazy */
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

/* Hlavné menu */
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

.main-nav-link:hover {
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

.main-nav-link:hover .nav-underline {
  width: 100%;
}

/* Hero sekcia */
.hero-section {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.particle {
  position: absolute;
  width: 4px;
  height: 4px;
  background: rgba(255, 255, 255, 0.5);
  border-radius: 50%;
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
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

.hero-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 3rem;
}

.hero-btn {
  padding: 1rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.hero-btn.primary {
  background: #f59e0b;
  color: black;
}

.hero-btn.primary:hover {
  background: #d97706;
  transform: translateY(-2px);
}

.hero-btn.secondary {
  background: rgba(255, 255, 255, 0.1);
  color: black;
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.hero-btn.secondary:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
}

.btn-arrow {
  transition: transform 0.3s ease;
}

.hero-btn:hover .btn-arrow {
  transform: translateX(3px);
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

.scroll-indicator {
  position: absolute;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  color: white;
  opacity: 0.7;
}

.scroll-arrow {
  font-size: 1.5rem;
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-10px); }
  60% { transform: translateY(-5px); }
}

/* Section Headers */
.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.1rem;
  color: #6b7280;
  max-width: 600px;
  margin: 0 auto;
}

.section-footer {
  text-align: center;
  margin-top: 3rem;
}

/* Najnovšie články */
.latest-news {
  padding: 6rem 0;
  background: #f8fafc;
}

.news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
}

.news-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border: 1px solid #e5e7eb;
}

.news-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.news-card.featured {
  grid-column: span 2;
}

.news-content {
  padding: 2rem;
}

.news-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.news-category {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-weight: 500;
}

.news-date {
  color: #6b7280;
}

.news-card h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
  line-height: 1.3;
}

.news-card.featured h3 {
  font-size: 1.875rem;
}

.news-author {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.9rem;
  margin-bottom: 1rem;
}

.author-icon {
  font-size: 1rem;
}

.news-excerpt {
  color: #6b7280;
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.read-more {
  color: #2563eb;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.read-more:hover {
  color: #1d4ed8;
}

.arrow {
  transition: transform 0.3s ease;
}

.read-more:hover .arrow {
  transform: translateX(3px);
}

/* Loading, Error, Empty States */
.loading-state,
.error-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #2563eb;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-icon,
.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.error-state h3,
.empty-state h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.retry-button {
  margin-top: 1rem;
  padding: 0.75rem 1.5rem;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.retry-button:hover {
  background: #1d4ed8;
}

/* Vítacia sekcia */
.welcome-section {
  padding: 6rem 0;
  background: white;
}

.welcome-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}

.welcome-text h2 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.welcome-text p {
  font-size: 1.1rem;
  color: #6b7280;
  line-height: 1.7;
}

.highlight-text {
  color: #f59e0b;
  font-weight: 600;
}

.welcome-features {
  display: grid;
  gap: 2rem;
}

.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1.5rem;
  background: #f8fafc;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.feature-item:hover {
  background: #f1f5f9;
  transform: translateY(-2px);
}

.feature-icon {
  font-size: 2rem;
  flex-shrink: 0;
}

.feature-item h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.feature-item p {
  color: #6b7280;
  font-size: 0.95rem;
}

/* Výskumné oblasti */
.research-areas {
  padding: 6rem 0;
  background: #f8fafc;
}

.research-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.research-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border: 1px solid #e5e7eb;
  position: relative;
  overflow: hidden;
}

.research-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.research-card:hover::before {
  transform: scaleX(1);
}

.research-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.research-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.research-icon.life-sciences { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.research-icon.data-science { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.research-icon.environmental { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.research-icon.materials { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.research-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.research-card p {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.card-footer {
  margin-top: auto;
}

.learn-more {
  color: #2563eb;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.learn-more:hover {
  color: #1d4ed8;
}

/* Responzívny dizajn */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 1rem;
  }
  
  .hero-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .welcome-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .news-card.featured {
    grid-column: span 1;
  }
  
  .news-grid {
    grid-template-columns: 1fr;
  }
}
</style>