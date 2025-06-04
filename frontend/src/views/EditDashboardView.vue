<template>
  <div class="dashboard-container">
    <NavbarComponent />
    
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="hero-particles">
          <div class="particle" v-for="i in 20" :key="i"></div>
        </div>
      </div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">
            <span class="title-line">Editor</span>
            <span class="title-line highlight">Dashboard</span>
          </h1>
          <p class="hero-subtitle">
            Spravujte články pre svoje priradené ročníky
          </p>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">{{ assignedYears.length }}</span>
              <span class="stat-label">Priradené ročníky</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ articles.length }}</span>
              <span class="stat-label">Články</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ articlesInSelectedYear.length }}</span>
              <span class="stat-label">Články v ročníku</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Filter Section -->
    <section class="management-section">
      <div class="container">
        <div class="filter-controls">
          <div class="form-group">
            <label>Ročník konferencie:</label>
            <select v-model="selectedYearId" @change="loadArticlesForYear" class="modern-select">
              <option value="">Vyberte ročník</option>
              <option v-for="year in assignedYears" :key="year.id" :value="year.id">
                {{ year.semester }} {{ year.year }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <!-- Articles Management -->
    <section class="management-section" v-if="selectedYearId">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Články pre {{ selectedYearName }}</h2>
          <p class="section-subtitle">Spravujte články pre vybraný ročník</p>
        </div>
        
        <div class="management-content">
          <div class="management-actions">
            <button @click="openArticleModal()" class="hero-btn primary">
              Pridať článok
            </button>
          </div>

          <div v-if="loading" class="loading-state">
            <div class="loading-spinner"></div>
            <h3>Načítavam články...</h3>
          </div>

          <div v-else-if="articlesInSelectedYear.length === 0" class="empty-state">
            <div class="empty-icon">📄</div>
            <h3>Žiadne články</h3>
            <p>Pre tento ročník ešte neboli vytvorené žiadne články.</p>
          </div>

          <div v-else class="cards-grid">
            <div v-for="article in articlesInSelectedYear" :key="article.id" class="feature-card article-card">
              <div class="card-header">
                <h3>{{ article.title }}</h3>
                <span class="meta-item">{{ article.author_name }}</span>
              </div>
              
              <div class="card-content">
                <div class="content-preview">
                  <p>{{ formatArticleSummary(article.content) }}</p>
                </div>
                <div class="card-meta">
                  <div class="meta-item">
                    <strong>Autor:</strong> {{ article.author_name }}
                  </div>
                  <div class="meta-item">
                    <strong>Vytvorené:</strong> {{ formatDate(article.created_at) }}
                  </div>
                </div>
              </div>

              <div class="card-actions">
                <button @click="openArticleModal(article)" class="action-btn edit">Upraviť</button>
                <button @click="deleteArticle(article.id)" class="action-btn delete">Vymazať</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- No Year Selected State -->
    <section v-else class="management-section">
      <div class="container">
        <div class="empty-state">
          <div class="empty-icon">📅</div>
          <h3>Vyberte ročník</h3>
          <p>Vyberte ročník konferencie zo zoznamu vyššie pre zobrazenie článkov.</p>
        </div>
      </div>
    </section>

    <!-- Article Modal -->
    <div v-if="showArticleForm" class="modal-overlay">
      <div class="modal-container">
        <div class="modal-header">
          <h3>{{ editArticle ? 'Upraviť článok' : 'Pridať článok' }}</h3>
          <button @click="closeArticleModal" class="close-button">×</button>
        </div>
        <form @submit.prevent="saveArticle" class="modal-form">
          <div class="form-group">
            <label>Názov článku</label>
            <input v-model="formArticle.title" type="text" required class="modern-input" />
          </div>
          <div class="form-group">
            <label>Autor</label>
            <input v-model="formArticle.author_name" type="text" required class="modern-input" />
          </div>
          <div class="form-group">
            <label>Obsah</label>
            <Editor
              v-model="formArticle.content"
              :api-key="tinymceKey"
              :init="tinymceConfig"
            />
          </div>
          <div class="form-actions">
            <button type="button" @click="closeArticleModal" class="hero-btn secondary">Zrušiť</button>
            <button type="submit" class="hero-btn primary">{{ editArticle ? 'Uložiť' : 'Vytvoriť' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import Editor from '@tinymce/tinymce-vue'
import { articleApi, type Article } from '@/services/article'
import adminPanel from '@/services/adminPanel'
import NavbarComponent from '@/components/NavbarComponent.vue'
import type { Settings } from 'tinymce'

export default defineComponent({
  name: 'EditDashboardView',
  components: { 
    NavbarComponent,
    Editor
  },
  
  data() {
    return {
      assignedYears: [] as any[],
      articles: [] as Article[],
      selectedYearId: null as number | null,
      loading: false,
      
      showArticleForm: false,
      editArticle: null as Article | null,
      formArticle: {
        title: '',
        content: '',
        author_name: ''
      },

      // TinyMCE configuration
      tinymceKey: 'ama3uyd2ecm9bw4zvg1689uk4qkpcxzv7sxvjv47ylo35cen',
      tinymceConfig: {
        height: 400,
        menubar: false,
        plugins: [
          'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
          'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
          'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        images_upload_handler: async (blobInfo: any, progress: any) => {
          return new Promise(async (resolve, reject) => {
            try {
              const formData = new FormData();
              formData.append('file', blobInfo.blob(), blobInfo.filename());
              
              const response = await adminPanel.uploadImage(formData);
              resolve(response.data.location);
            } catch (error) {
              console.error('Upload error:', error);
              reject('Image upload failed');
            }
          });
        }
      } as Settings
    }
  },
  
  computed: {
    articlesInSelectedYear(): Article[] {
      if (!this.selectedYearId) return []
      return this.articles.filter(article => article.conference_year_id === this.selectedYearId)
    },

    selectedYearName(): string {
      if (!this.selectedYearId) return ''
      const year = this.assignedYears.find(y => y.id === this.selectedYearId)
      return year ? `${year.semester} ${year.year}` : ''
    }
  },
  
  methods: {
    async loadAssignedYears() {
      try {
        // Získame prihláseneho používateľa
        const userResponse = await fetch('/api/user', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json'
          }
        })
        
        if (!userResponse.ok) {
          throw new Error('Failed to get user info')
        }
        
        const userData = await userResponse.json()
        const userId = userData.user.id

        // Načítame všetky ročníky
        const yearsResponse = await adminPanel.getConferenceYears()
        const allYears = yearsResponse.data?.data || []

        // Pre každý ročník zistíme, či je používateľ priradený
        const assigned = []
        for (const year of allYears) {
          try {
            const assignmentsResponse = await adminPanel.listYearEditors(year.id)
            const assignments = assignmentsResponse.data || []
            
            // Skontrolujeme, či je aktuálny používateľ v zozname editorov
            const isAssigned = assignments.some((assignment: any) => assignment.user_id === userId)
            
            if (isAssigned) {
              assigned.push(year)
            }
          } catch (error) {
            console.error(`Error checking assignments for year ${year.id}:`, error)
          }
        }

        this.assignedYears = assigned
      } catch (error) {
        console.error('Error loading assigned years:', error)
        this.assignedYears = []
      }
    },

    async loadArticlesForYear() {
      if (!this.selectedYearId) {
        this.articles = []
        return
      }

      this.loading = true
      try {
        const response = await adminPanel.listArticles({ conference_year_id: this.selectedYearId })
        this.articles = response.data?.data || []
      } catch (error) {
        console.error('Error loading articles:', error)
        this.articles = []
      } finally {
        this.loading = false
      }
    },

    openArticleModal(article: Article | null = null) {
      this.editArticle = article
      if (article) {
        this.formArticle = {
          title: article.title,
          content: article.content || '',
          author_name: article.author_name
        }
      } else {
        this.formArticle = {
          title: '',
          content: '',
          author_name: ''
        }
      }
      this.showArticleForm = true
    },

    closeArticleModal() {
      this.showArticleForm = false
      this.editArticle = null
      this.formArticle = {
        title: '',
        content: '',
        author_name: ''
      }
    },

    async saveArticle() {
      if (!this.selectedYearId) {
        alert('Vyberte ročník konferencie')
        return
      }

      try {
        const articleData = {
          ...this.formArticle,
          conference_year_id: this.selectedYearId
        }

        if (this.editArticle) {
          await adminPanel.updateArticle(this.editArticle.id, articleData)
        } else {
          await adminPanel.createArticle(articleData)
        }

        await this.loadArticlesForYear()
        this.closeArticleModal()
      } catch (error) {
        console.error('Error saving article:', error)
        alert('Chyba pri ukladaní článku')
      }
    },

    async deleteArticle(id: number) {
      if (confirm('Naozaj chcete vymazať tento článok?')) {
        try {
          await adminPanel.deleteArticle(id)
          await this.loadArticlesForYear()
        } catch (error) {
          console.error('Error deleting article:', error)
          alert('Chyba pri mazaní článku')
        }
      }
    },

    formatDate(dateString?: string): string {
      if (!dateString) return 'Neznámy dátum'
      return new Date(dateString).toLocaleDateString('sk-SK')
    },

    formatArticleSummary(content: string | null): string {
      if (!content) return 'Žiadny obsah...'
      const cleanContent = content.replace(/<[^>]*>/g, '')
      return cleanContent.length > 150 ? cleanContent.substring(0, 150) + '...' : cleanContent
    }
  },
  
  async mounted() {
    await this.loadAssignedYears()
    
    // Ak je len jeden priradený ročník, automaticky ho vyberieme
    if (this.assignedYears.length === 1) {
      this.selectedYearId = this.assignedYears[0].id
      await this.loadArticlesForYear()
    }
  }
})
</script>

<style scoped>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap');

/* CSS Variables */
:root {
  --primary-color: #2563eb;
  --secondary-color: #f8fafc;
  --accent-color: #f59e0b;
  --success-color: #10b981;
  --danger-color: #ef4444;
  --warning-color: #f59e0b;
  --info-color: #3b82f6;
  --dark-color: #1f2937;
  --light-color: #f9fafb;
  --border-color: #e5e7eb;
  --text-primary: #111827;
  --text-secondary: #6b7280;
  --text-muted: #9ca3af;
  --white: #ffffff;
  --light-bg: #f8f9fa;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dashboard-container {
  font-family: 'Inter', 'Poppins', system-ui, -apple-system, sans-serif;
  color: var(--text-primary);
  background-color: var(--light-color);
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Hero Section - adjust top padding to account for navbar */
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem 0; /* Reduced top padding since navbar is now at the top */
  overflow: hidden;
  color: white;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.1);
}

.hero-particles {
  position: absolute;
  width: 100%;
  height: 100%;
  background-image: 
    radial-gradient(1px 1px at 20px 30px, rgba(255, 255, 255, 0.2), transparent),
    radial-gradient(1px 1px at 40px 70px, rgba(255, 255, 255, 0.1), transparent),
    radial-gradient(1px 1px at 90px 40px, rgba(255, 255, 255, 0.1), transparent);
  animation: float 20s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(180deg); }
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  line-height: 1.1;
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

/* Management Sections */
.management-section {
  padding: 4rem 0;
  background-color: var(--white);
}

.management-section.alt-bg {
  background-color: var(--light-bg);
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.1rem;
  color: var(--text-secondary);
  max-width: 600px;
  margin: 0 auto;
}

.management-content {
  max-width: 1000px;
  margin: 0 auto;
}

.management-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.search-container {
  flex: 1;
  max-width: 300px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.filter-controls {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
  margin-bottom: 2rem;
}

.filter-controls .form-group {
  margin-bottom: 0;
}

.filter-controls label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  display: block;
}

.hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: 1px solid transparent;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.hero-btn.primary {
  background-color: var(--primary-color);
  color: black;
  border: 2px solid #000;
}

.hero-btn.primary:hover {
  background-color: rgba(6, 218, 255, 0.877);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border: none;
}

.hero-btn.secondary {
  background-color: var(--border-color);
  color: var(--text-primary);
}

.hero-btn.secondary:hover {
  background-color: #d1d5db;
  transform: translateY(-1px);
}

.hero-btn.primary.delete-confirm {
  background-color: var(--danger-color);
  color: white;
  border: 2px solid #000;
}

.hero-btn.primary.delete-confirm:hover {
  background-color: #dc2626;
  border: none;
}

/* Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.feature-card {
  background: var(--white);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 1.5rem;
  transition: all 0.3s ease;
  box-shadow: var(--shadow-sm);
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.feature-card.year-card {
  cursor: pointer;
}

.feature-card.year-card.active {
  border-color: var(--primary-color);
  background-color: #f0f9ff;
}

.feature-card.article-card.draft {
  border-left: 4px solid #ffc107;
}

.feature-card .card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.feature-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  background-color: #fef3c7;
  color: #92400e;
}

.status-badge.active {
  background-color: #d1fae5;
  color: #065f46;
}

.year-info p {
  color: var(--text-secondary);
  margin: 0;
  font-size: 0.9rem;
}

.card-content {
  margin-bottom: 1rem;
}

.content-preview {
  color: var(--text-secondary);
  line-height: 1.5;
  margin: 0 0 1rem 0;
  font-size: 0.9rem;
}

.card-meta {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.meta-item {
  font-size: 0.8rem;
  color: var(--text-muted);
}

/* Action Buttons */
.action-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-right: 0.5rem;
}

.action-btn.edit {
  background-color: var(--info-color);
  color: black;
  border: 2px solid #000;
  border-radius: 8px;
}

.action-btn.edit:hover {
  background-color: rgb(2, 204, 2);
  border: none;
}

.action-btn.delete {
  background-color: var(--danger-color);
  color: black;
  border: 2px solid #000;
  border-radius: 8px;
}

.action-btn.delete:hover {
  background-color: #dc2626;
  border: none;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}

/* Select Controls */
.modern-select {
  padding: 0.75rem;
  border: 2px solid #000000;
  border-radius: 8px;
  font-size: 1rem;
  background-color: var(--white);
  color: var(--text-primary);
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 200px;
}

.modern-select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Form Sections */
.form-section {
  margin-bottom: 2rem;
}

.form-card {
  background: var(--white);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

.articles-section {
  margin-top: 2rem;
}

/* Loading and Empty States */
.loading-state,
.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  background-color: white;
  border-radius: 8px;
  margin: 2rem 0;
}

.loading-spinner {
  width: 3rem;
  height: 3rem;
  border: 3px solid #f3f3f3;
  border-top: 3px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  margin-bottom: 1rem;
  color: var(--text-primary);
}

.empty-state p {
  color: var(--text-secondary);
}

/* Alert */
.alert {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.alert-danger {
  color: #721c24;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
}

.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.25rem;
  color: inherit;
}

.close-button:hover {
  opacity: 0.7;
}

/* Modals */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-container {
  background: var(--white);
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-lg);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
  background-color: white;
}

.modal-header h3 {
  margin: 0;
  color: var(--text-primary);
  font-size: 1.5rem;
}

.modal-form {
  padding: 1.5rem;
  background-color: white;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text-primary);
}

.modern-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #000000;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: var(--white);
  color: var(--text-primary);
}

.modern-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.warning-text {
  color: var(--danger-color);
  font-weight: 500;
  margin: 1rem 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-stats {
    gap: 2rem;
  }
  
  .stat-number {
    font-size: 2rem;
  }
  
  .management-actions {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-container {
    max-width: none;
  }
  
  .filter-controls {
    justify-content: stretch;
  }
  
  .modern-select {
    width: 100%;
  }
  
  .cards-grid {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 1rem;
  }
  
  .management-content {
    padding: 1rem;
  }
  
  .modal-container {
    margin: 0.5rem;
  }
}
</style>