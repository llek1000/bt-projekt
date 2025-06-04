<template>
  <div class="dashboard-container">
    <NavbarComponent />
    
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="hero-particles">
          <div class="particle" v-for="n in 20" :key="n"></div>
        </div>
      </div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">
            <span class="title-line">Editor</span>
            <span class="title-line highlight">Dashboard</span>
          </h1>
          <p class="hero-subtitle">
            Správa článkov a obsahu konferencie
          </p>
          
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">{{ totalAssignedArticles }}</span>
              <span class="stat-label">Pridelené články</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ assignments.length }}</span>
              <span class="stat-label">Pridelené ročníky</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ filteredArticles.length }}</span>
              <span class="stat-label">Moje články</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Article Management Section -->
    <section class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa článkov</h2>
          <p class="section-subtitle">Vytvárajte a upravujte články pre pridelené ročníky konferencie</p>
        </div>

        <div class="management-actions">
          <div class="search-container">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Hľadať články..."
              class="search-input"
            />
          </div>
          
          <div class="filter-controls">
            <div class="form-group">
              <label>Filtrovať podľa ročníka:</label>
              <select v-model="selectedConferenceYearId" class="modern-select">
                <option value="">Všetky ročníky</option>
                <option 
                  v-for="cy in assignedConferenceYears" 
                  :key="cy.id" 
                  :value="cy.id"
                >
                  {{ cy.semester }} {{ cy.year }}
                </option>
              </select>
            </div>
          </div>

          <button @click="openArticleModal()" class="hero-btn primary">
            Vytvoriť nový článok
          </button>
        </div>

        <!-- Articles Grid -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Načítavam články...</p>
        </div>

        <div v-else-if="filteredArticles.length === 0" class="empty-state">
          <div class="empty-icon">📝</div>
          <h3>Žiadne články</h3>
          <p>Zatiaľ nemáte žiadne články. Vytvorte prvý článok.</p>
        </div>

        <div v-else class="cards-grid">
          <div v-for="article in filteredArticles" :key="article.id" class="feature-card article-card">
            <div class="card-header">
              <h3>{{ article.title }}</h3>
              <div class="card-meta">
                <div class="meta-item">
                  <span class="meta-label">Autor:</span>
                  <span class="meta-value">{{ article.author_name }}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Ročník:</span>
                  <span class="meta-value">
                    {{ article.conference_year ? `${article.conference_year.semester} ${article.conference_year.year}` : 'N/A' }}
                  </span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Vytvorené:</span>
                  <span class="meta-value">{{ formatDate(article.created_at) }}</span>
                </div>
              </div>
            </div>
            
            <div class="card-content">
              <div class="content-preview">{{ getContentPreview(article.content) }}</div>
            </div>
            
            <div class="card-actions">
              <button @click="openArticleModal(article)" class="action-btn edit">
                Upraviť
              </button>
              <button @click="deleteArticle(article.id)" class="action-btn delete">
                Vymazať
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Article Modal -->
    <div v-if="showArticleModal" class="modal-overlay" @click="closeArticleModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>{{ editArticle ? 'Upraviť článok' : 'Vytvoriť nový článok' }}</h3>
          <button @click="closeArticleModal" class="close-button">&times;</button>
        </div>
        
        <form @submit.prevent="saveArticle" class="modal-form">
          <div class="form-group">
            <label>Názov článku:</label>
            <input
              v-model="articleForm.title"
              type="text"
              required
              class="modern-input"
              placeholder="Zadajte názov článku"
            />
          </div>

          <div class="form-group">
            <label>Autor:</label>
            <input
              v-model="articleForm.author_name"
              type="text"
              required
              class="modern-input"
              placeholder="Meno autora"
            />
          </div>

          <div class="form-group">
            <label>Ročník konferencie:</label>
            <select v-model="articleForm.conference_year_id" required class="modern-select">
              <option value="">Vyberte ročník</option>
              <option 
                v-for="cy in assignedConferenceYears" 
                :key="cy.id" 
                :value="cy.id"
              >
                {{ cy.semester }} {{ cy.year }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Obsah článku:</label>
            <Editor
              v-model="articleForm.content"
              :api-key="tinymceKey"
              :init="tinymceConfig"
            />
          </div>

          <div class="form-actions">
            <button type="button" @click="closeArticleModal" class="hero-btn secondary">
              Zrušiť
            </button>
            <button type="submit" class="hero-btn primary">
              {{ editArticle ? 'Uložiť zmeny' : 'Vytvoriť článok' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import Editor from '@tinymce/tinymce-vue'
import editorPanel from '@/services/editorPanel'
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
      // Loading states
      loading: false,
      
      // Data arrays
      assignments: [] as any[],
      articles: [] as any[],
      conferenceYears: [] as any[],
      
      // Form states
      showArticleModal: false,
      editArticle: null as any,
      articleForm: {
        title: '',
        content: '',
        author_name: '',
        conference_year_id: ''
      },
      
      // Filter states
      searchQuery: '',
      selectedConferenceYearId: '',
      
      // TinyMCE configuration
      tinymceKey: 'ama3uyd2ecm9bw4zvg1689uk4qkpcxzv7sxvjv47ylo35cen',
      tinymceConfig: {
        height: 400,
        menubar: true,
        plugins: [
          'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
          'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
          'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
        images_upload_handler: this.handleImageUpload,
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: this.handleFilePicker,
        images_upload_url: 'http://localhost/bt/bt-projekt/public/api/upload-image',
        images_upload_base_path: '',
        images_upload_credentials: true,
        setup: (editor: any) => {
          editor.on('init', () => {
            console.log('TinyMCE Editor initialized for EditDashboard')
          })
        }
      } as Settings
    }
  },

  computed: {
    // Get conference years that are assigned to current user
    assignedConferenceYears() {
      const assignedYearIds = this.assignments.map(a => a.conference_year_id)
      return this.conferenceYears.filter(cy => assignedYearIds.includes(cy.id))
    },

    // Filter articles based on assignments, search query and selected conference year
    filteredArticles() {
      let filtered = this.articles

      // Only show articles from assigned conference years
      const assignedYearIds = this.assignments.map(a => a.conference_year_id)
      filtered = filtered.filter(article => assignedYearIds.includes(article.conference_year_id))

      // Apply search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(article => 
          article.title.toLowerCase().includes(query) ||
          article.author_name.toLowerCase().includes(query) ||
          (article.content && article.content.toLowerCase().includes(query))
        )
      }

      // Apply conference year filter
      if (this.selectedConferenceYearId) {
        filtered = filtered.filter(article => 
          article.conference_year_id === parseInt(this.selectedConferenceYearId)
        )
      }

      return filtered
    },

    // Calculate total articles assigned to user
    totalAssignedArticles() {
      const assignedYearIds = this.assignments.map(a => a.conference_year_id)
      return this.articles.filter(article => assignedYearIds.includes(article.conference_year_id)).length
    }
  },

  methods: {
    // TinyMCE image upload handler
    async handleImageUpload(blobInfo: any, progress: any): Promise<string> {
      return new Promise(async (resolve, reject) => {
        try {
          const formData = new FormData()
          formData.append('file', blobInfo.blob(), blobInfo.filename())

          const response = await editorPanel.uploadImage(formData)
          
          if (response.data && response.data.location) {
            resolve(response.data.location)
          } else {
            reject('Upload failed: No location returned')
          }
        } catch (error) {
          console.error('Image upload error:', error)
          reject('Upload failed: ' + (error.response?.data?.error || error.message))
        }
      })
    },

    // TinyMCE file picker callback
    handleFilePicker(callback: any, value: any, meta: any) {
      if (meta.filetype === 'image') {
        const input = document.createElement('input')
        input.setAttribute('type', 'file')
        input.setAttribute('accept', 'image/*')

        input.onchange = async () => {
          const file = input.files?.[0]
          if (file) {
            try {
              const formData = new FormData()
              formData.append('file', file)

              const response = await editorPanel.uploadImage(formData)
              
              if (response.data && response.data.location) {
                callback(response.data.location, { title: file.name })
              }
            } catch (error) {
              console.error('File picker upload error:', error)
              alert('Chyba pri nahrávaní obrázka: ' + (error.response?.data?.error || error.message))
            }
          }
        }

        input.click()
      }
    },

    // Load user assignments
    async loadAssignments() {
      try {
        const response = await editorPanel.getMyAssignments()
        this.assignments = response.data.data || []
        console.log('Loaded assignments:', this.assignments)
      } catch (error) {
        console.error('Error loading assignments:', error)
      }
    },

    // Load conference years
    async loadConferenceYears() {
      try {
        const response = await editorPanel.getConferenceYears()
        this.conferenceYears = response.data.data || []
        console.log('Loaded conference years:', this.conferenceYears)
      } catch (error) {
        console.error('Error loading conference years:', error)
      }
    },

    // Load articles
    async loadArticles() {
      try {
        this.loading = true
        const response = await editorPanel.listArticles()
        this.articles = response.data.data || []
        console.log('Loaded articles:', this.articles)
      } catch (error) {
        console.error('Error loading articles:', error)
      } finally {
        this.loading = false
      }
    },

    // Open article modal for create/edit
    openArticleModal(article = null) {
      this.editArticle = article
      if (article) {
        this.articleForm = {
          title: article.title || '',
          content: article.content || '',
          author_name: article.author_name || '',
          conference_year_id: article.conference_year_id?.toString() || ''
        }
      } else {
        this.articleForm = {
          title: '',
          content: '',
          author_name: '',
          conference_year_id: ''
        }
      }
      this.showArticleModal = true
    },

    // Close article modal
    closeArticleModal() {
      this.showArticleModal = false
      this.editArticle = null
      this.articleForm = {
        title: '',
        content: '',
        author_name: '',
        conference_year_id: ''
      }
    },

    // Save article (create or update)
    async saveArticle() {
      try {
        const articleData = {
          title: this.articleForm.title,
          content: this.articleForm.content,
          author_name: this.articleForm.author_name,
          conference_year_id: parseInt(this.articleForm.conference_year_id)
        }

        if (this.editArticle) {
          await editorPanel.updateArticle(this.editArticle.id, articleData)
        } else {
          await editorPanel.createArticle(articleData)
        }

        await this.loadArticles()
        this.closeArticleModal()
      } catch (error) {
        console.error('Error saving article:', error)
        alert('Chyba pri ukladaní článku: ' + (error.response?.data?.message || error.message))
      }
    },

    // Delete article
    async deleteArticle(id: number) {
      if (!confirm('Naozaj chcete vymazať tento článok?')) return

      try {
        await editorPanel.deleteArticle(id)
        await this.loadArticles()
      } catch (error) {
        console.error('Error deleting article:', error)
        alert('Chyba pri mazaní článku: ' + (error.response?.data?.message || error.message))
      }
    },

    // Helper methods
    formatDate(dateString?: string): string {
      return editorPanel.helpers.formatDate(dateString)
    },

    getContentPreview(content: string): string {
      return editorPanel.helpers.getContentPreview(content, 150)
    }
  },

  async mounted() {
    await Promise.all([
      this.loadAssignments(),
      this.loadConferenceYears(),
      this.loadArticles()
    ])
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');

:root {
  --primary-color: #2563eb;
  --secondary-color: #f8fafc;
  --accent-color: #f59e0b;
  --success-color: #10b981;
  --danger-color: #ef4444;
  --dark-color: #1f2937;
  --light-color: #f9fafb;
  --border-color: #e5e7eb;
  --text-primary: #111827;
  --text-secondary: #6b7280;
  --white: #ffffff;
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dashboard-container {
  font-family: 'Inter', system-ui, sans-serif;
  color: var(--text-primary);
  background-color: var(--light-color);
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Hero Section */
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 6rem 0 4rem;
  color: white;
  overflow: hidden;
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

.particle:nth-child(odd) {
  animation-delay: -2s;
}

.particle:nth-child(even) {
  animation-delay: -4s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
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

/* Management Section */
.management-section {
  padding: 4rem 0;
  background: white;
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.1rem;
  color: var(--text-secondary);
}

.management-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.search-container {
  flex: 1;
  min-width: 250px;
  border: 1.5px solid #111;
  border-radius: 25px;
  background: white;
  padding: 0.25rem 0.5rem;
  display: flex;
  align-items: center;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: none;
  background: transparent;
  font-size: 0.95rem;
  border-radius: 25px;
  outline: none;
  color: var(--text-primary);
}

.search-input:focus {
  background: #f3f4f6;
}

.filter-controls {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.filter-controls .form-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0;
}

.filter-controls label {
  font-size: 0.9rem;
  color: var(--text-secondary);
  white-space: nowrap;
}

.hero-btn, .btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.hero-btn.primary, .btn.primary {
  background: var(--primary-color);
  color: black;
  border: 1.5px solid #111;
  border-radius: 25px;
}

.hero-btn.primary:hover, .btn.primary:hover {
  background: #1d4ed8;
  border: none;
}

.hero-btn.secondary, .btn.secondary {
  background: var(--text-secondary);
  color: black;
  border: 1.5px solid #111;
  border-radius: 25px;
}

.hero-btn.secondary:hover, .btn.secondary:hover {
  background: red;
  border: none;
}

/* Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1.5rem;
}

.feature-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: var(--shadow-md);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  display: flex;
  flex-direction: column;
  height: 100%;
}

.feature-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.card-header {
  margin-bottom: 1rem;
}

.feature-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.card-meta {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 0.75rem;
  padding-top: 0.75rem;
  border-top: 1px solid var(--border-color);
}

.meta-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
}

.meta-label {
  font-weight: 500;
  color: var(--text-secondary);
}

.meta-value {
  color: var(--text-primary);
}

.card-content {
  flex: 1;
  padding: 1rem 0;
}

.content-preview {
  color: var(--text-secondary);
  font-size: 0.9rem;
  line-height: 1.5;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

.action-btn {
  flex: 1;
  padding: 0.5rem 1rem;
  border: 1px solid #111;
  border-radius: 25px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  color: black;
}

.action-btn.edit {
  background: var(--primary-color);
}

.action-btn.edit:hover {
  background: rgb(0, 162, 255);
  border: none;
}

.action-btn.delete {
  background: var(--danger-color);
}

.action-btn.delete:hover {
  background: #dc2626;
  border: none;
}

/* Form Controls */
.modern-select, .modern-input {
  padding: 0.75rem 1rem;
  border: 1px solid #111;
  border-radius: 25px;
  font-size: 1rem;
  transition: all 0.2s ease;
  background: white;
  width: 100%;
  box-sizing: border-box;
}

.modern-select {
  cursor: pointer;
}

.modern-input:focus, .modern-select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Loading and Empty States */
.loading-state {
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
  border: 4px solid var(--border-color);
  border-top: 4px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
  color: var(--text-primary);
}

.empty-state p {
  font-size: 0.95rem;
}

/* Modal Styles */
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
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 900px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--border-color);
}

.modal-header h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.close-button:hover {
  background: var(--light-color);
  color: var(--text-primary);
}

.modal-form {
  padding: 2rem;
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

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border-color);
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 1rem;
  }
  
  .management-actions {
    flex-direction: column;
    gap: 1rem;
  }
  
  .cards-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-container {
    margin: 1rem;
    max-width: calc(100% - 2rem);
  }

  .modal-header,
  .modal-form {
    padding: 1rem;
  }
}
</style>
