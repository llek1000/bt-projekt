<template>
  <div class="page-container">
    <NavbarComponent />
    
    <main class="main-content">
      <!-- Breadcrumb Navigation -->
      <section class="breadcrumb-section">
        <div class="container">
          <nav class="breadcrumb">
            <router-link to="/" class="breadcrumb-link">Domov</router-link>
            <span class="breadcrumb-separator">›</span>
            <router-link to="/publications" class="breadcrumb-link">Publikácie</router-link>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">{{ article?.title || 'Načítava...' }}</span>
          </nav>
        </div>
      </section>

      <!-- Article Content -->
      <section class="article-section">
        <div class="container">
          <div v-if="loading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Načítavam článok...</p>
          </div>

          <div v-else-if="error" class="error-state">
            <div class="error-icon">⚠️</div>
            <h3>Chyba pri načítavaní článku</h3>
            <p>{{ error }}</p>
            <button @click="loadArticle" class="retry-button">
              Skúsiť znovu
            </button>
          </div>

          <article v-else-if="article" class="article-content">
            <!-- Article Header -->
            <header class="article-header">
              <div class="article-meta">
                <div class="meta-item">
                  <span class="meta-label">Konferencia:</span>
                  <span class="meta-value">
                    {{ article.conference_year?.semester }} {{ article.conference_year?.year }}
                  </span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Autor:</span>
                  <span class="meta-value">{{ article.author_name }}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Publikované:</span>
                  <span class="meta-value">{{ formatDate(article.created_at) }}</span>
                </div>
              </div>
              
              <h1 class="article-title">{{ article.title }}</h1>
            </header>

            <!-- Article Body -->
            <div class="article-body">
              <div v-if="article.content" class="content-body" v-html="article.content"></div>
              <div v-else class="no-content">
                <p>Obsah článku nie je dostupný.</p>
              </div>
            </div>

            <!-- Article Footer -->
            <footer class="article-footer">
              <div class="article-actions">
                <button @click="$router.go(-1)" class="back-button">
                  ← Späť na publikácie
                </button>
                <button @click="shareArticle" class="share-button">
                  📤 Zdieľať článok
                </button>
              </div>
            </footer>
          </article>

          <div v-else class="not-found-state">
            <div class="not-found-icon">📄</div>
            <h3>Článok nebol nájdený</h3>
            <p>Článok s ID {{ $route.params.id }} neexistuje.</p>
            <router-link to="/publications" class="back-link">
              ← Späť na publikácie
            </router-link>
          </div>
        </div>
      </section>

      <!-- Related Articles -->
      <section v-if="relatedArticles.length > 0" class="related-section">
        <div class="container">
          <h2 class="section-title">Súvisiace články</h2>
          <div class="related-grid">
            <div v-for="relatedArticle in relatedArticles" :key="relatedArticle.id" class="related-card">
              <div class="related-meta">
                <span class="related-author">{{ relatedArticle.author_name }}</span>
                <span class="related-date">{{ formatDate(relatedArticle.created_at) }}</span>
              </div>
              <h3 class="related-title">
                <router-link :to="`/articles/${relatedArticle.id}`">
                  {{ relatedArticle.title }}
                </router-link>
              </h3>
              <p class="related-excerpt">{{ getExcerpt(relatedArticle.content) }}</p>
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
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

export default {
  name: 'ArticleView',
  components: {
    NavbarComponent,
    FooterComponent
  },
  
  data() {
    return {
      article: null,
      relatedArticles: [],
      loading: true,
      error: null
    }
  },
  
  async mounted() {
    await this.loadArticle()
    await this.loadRelatedArticles()
  },
  
  watch: {
    '$route.params.id': {
      handler() {
        this.loadArticle()
        this.loadRelatedArticles()
      }
    }
  },
  
  methods: {
    async loadArticle() {
      try {
        this.loading = true
        this.error = null
        
        const articleId = this.$route.params.id
        this.article = await articleApi.getArticle(articleId)
        
        if (!this.article) {
          this.error = 'Článok nebol nájdený'
        }
        
      } catch (error) {
        console.error('Error loading article:', error)
        this.error = error.response?.data?.message || 'Nepodarilo sa načítať článok'
      } finally {
        this.loading = false
      }
    },
    
    async loadRelatedArticles() {
      try {
        if (!this.article?.conference_year_id) return
        
        const articles = await articleApi.getArticlesByConferenceYear(this.article.conference_year_id)
        
        // Filter out current article and limit to 3
        this.relatedArticles = articles.articles
          ?.filter(a => a.id !== this.article.id)
          ?.slice(0, 3) || []
          
      } catch (error) {
        console.error('Error loading related articles:', error)
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return 'Neznámy dátum'
      
      return new Date(dateString).toLocaleDateString('sk-SK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    
    getExcerpt(content) {
      if (!content) return 'Žiadny obsah...'
      
      // Remove HTML tags and limit to 150 characters
      const plainText = content.replace(/<[^>]*>/g, '')
      return plainText.length > 150 
        ? plainText.substring(0, 150) + '...'
        : plainText
    },
    
    shareArticle() {
      if (navigator.share) {
        navigator.share({
          title: this.article.title,
          text: `Článok od ${this.article.author_name}`,
          url: window.location.href
        })
      } else {
        // Fallback - copy URL to clipboard
        navigator.clipboard.writeText(window.location.href)
        alert('URL článku bola skopírovaná do schránky!')
      }
    }
  }
}
</script>

<style scoped>
/* CSS Variables */
:root {
  --primary-color: #2563eb;
  --primary-dark: #1d4ed8;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --text-light: #9ca3af;
  --bg-primary: #ffffff;
  --bg-secondary: #f8fafc;
  --border-color: #e5e7eb;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Base Styles */
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
  padding: 0 1rem;
}

.main-content {
  flex: 1;
  padding-top: 2rem;
}

/* Breadcrumb */
.breadcrumb-section {
  background: var(--bg-secondary);
  padding: 1rem 0;
  border-bottom: 1px solid var(--border-color);
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.breadcrumb-link {
  color: var(--primary-color);
  text-decoration: none;
  transition: color 0.2s ease;
}

.breadcrumb-link:hover {
  color: var(--primary-dark);
}

.breadcrumb-separator {
  color: var(--text-light);
}

.breadcrumb-current {
  color: var(--text-secondary);
  font-weight: 500;
}

/* Article Section */
.article-section {
  padding: 3rem 0;
}

.article-content {
  max-width: 800px;
  margin: 0 auto;
  background: var(--bg-primary);
  border-radius: 12px;
  box-shadow: var(--shadow-lg);
  overflow: hidden;
}

/* Article Header */
.article-header {
  padding: 3rem 3rem 2rem;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-bottom: 1px solid var(--border-color);
}

.article-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.meta-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.meta-label {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-light);
}

.meta-value {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-secondary);
}

.article-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1.2;
  margin: 0;
}

/* Article Body */
.article-body {
  padding: 3rem;
}

.content-body {
  font-size: 1.125rem;
  line-height: 1.8;
  color: var(--text-primary);
}

.content-body h1,
.content-body h2,
.content-body h3,
.content-body h4,
.content-body h5,
.content-body h6 {
  margin: 2rem 0 1rem;
  font-weight: 600;
  color: var(--text-primary);
}

.content-body h1 { font-size: 2rem; }
.content-body h2 { font-size: 1.75rem; }
.content-body h3 { font-size: 1.5rem; }

.content-body p {
  margin-bottom: 1.5rem;
}

.content-body ul,
.content-body ol {
  margin: 1.5rem 0;
  padding-left: 2rem;
}

.content-body li {
  margin-bottom: 0.5rem;
}

.content-body img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 2rem 0;
  box-shadow: var(--shadow-md);
}

.content-body blockquote {
  border-left: 4px solid var(--primary-color);
  padding-left: 1.5rem;
  margin: 2rem 0;
  font-style: italic;
  color: var(--text-secondary);
}

.no-content {
  text-align: center;
  padding: 3rem 0;
  color: var(--text-light);
}

/* Article Footer */
.article-footer {
  padding: 2rem 3rem 3rem;
  border-top: 1px solid var(--border-color);
}

.article-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.back-button,
.share-button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.back-button {
  background: var(--bg-secondary);
  color: var(--text-secondary);
  border: 1px solid var(--border-color);
}

.back-button:hover {
  background: var(--border-color);
  color: var(--text-primary);
}

.share-button {
  background: var(--primary-color);
  color: white;
}

.share-button:hover {
  background: var(--primary-dark);
}

/* Related Articles */
.related-section {
  padding: 3rem 0;
  background: var(--bg-secondary);
}

.section-title {
  font-size: 2rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 3rem;
  color: var(--text-primary);
}

.related-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.related-card {
  background: var(--bg-primary);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.related-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.related-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  font-size: 0.875rem;
  color: var(--text-light);
}

.related-title {
  margin: 0 0 1rem;
  font-size: 1.25rem;
  font-weight: 600;
}

.related-title a {
  color: var(--text-primary);
  text-decoration: none;
  transition: color 0.2s ease;
}

.related-title a:hover {
  color: var(--primary-color);
}

.related-excerpt {
  color: var(--text-secondary);
  margin: 0;
}

/* Loading, Error & Not Found States */
.loading-state,
.error-state,
.not-found-state {
  text-align: center;
  padding: 4rem 2rem;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--border-color);
  border-top: 4px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

.error-icon,
.not-found-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.error-state h3,
.not-found-state h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: var(--text-primary);
}

.retry-button,
.back-link {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background: var(--primary-color);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
  transition: background-color 0.2s ease;
  border: none;
  cursor: pointer;
  margin-top: 1rem;
}

.retry-button:hover,
.back-link:hover {
  background: var(--primary-dark);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .article-header,
  .article-body,
  .article-footer {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
  
  .article-title {
    font-size: 2rem;
  }
  
  .article-meta {
    flex-direction: column;
    gap: 1rem;
  }
  
  .article-actions {
    flex-direction: column;
  }
  
  .related-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .breadcrumb {
    flex-wrap: wrap;
  }
  
  .article-header,
  .article-body,
  .article-footer {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .article-title {
    font-size: 1.75rem;
  }
}
</style>