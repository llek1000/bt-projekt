<template>
  <div class="dashboard-container">
    <NavbarComponent />
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background"></div>
      <div class="hero-overlay"></div>
      <div class="hero-particles"></div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">
            <span class="title-line">Editor</span>
            <span class="title-line highlight">Dashboard</span>
          </h1>
          <p class="hero-subtitle">Správa článkov pre vaše priradené ročníky konferencie</p>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">{{ assignedYears.length }}</span>
              <span class="stat-label">Ročníkov</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ articles.length }}</span>
              <span class="stat-label">Článkov</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ filteredArticles.length }}</span>
              <span class="stat-label">Zobrazených</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Error Alert -->
    <div v-if="error" class="management-section alt-bg">
      <div class="container">
        <div class="alert alert-danger">
          <span>{{ error }}</span>
          <button @click="clearError" class="close-button">&times;</button>
        </div>
      </div>
    </div>

    <!-- Conference Years Selection -->
    <div class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Výber ročníka konferencie</h2>
          <p class="section-subtitle">Vyberte ročník pre správu článkov</p>
        </div>

        <div class="management-content">
          <div v-if="loadingYears" class="loading-state">
            <p>Načítavanie ročníkov konferencie...</p>
          </div>

          <div v-else-if="assignedYears.length === 0" class="empty-state">
            <div class="empty-icon">📅</div>
            <h3>Žiadne dostupné ročníky</h3>
            <p>Nemáte priradené žiadne ročníky konferencie.</p>
          </div>

          <div v-else class="cards-grid">
            <div
              v-for="year in assignedYears"
              :key="year.id"
              @click="selectYear(year.id)"
              class="feature-card year-card"
              :class="{ active: selectedYearId === year.id }"
            >
              <div class="card-header">
                <h3>{{ year.semester }} {{ year.year }}</h3>
                <span class="status-badge" :class="{ active: year.is_active }">
                  {{ year.is_active ? 'Aktívny' : 'Neaktívny' }}
                </span>
              </div>
              <div class="year-info">
                <p>Kliknite pre správu článkov tohto ročníka</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Article Management -->
    <div v-if="selectedYearId" class="management-section alt-bg">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa článkov</h2>
          <p class="section-subtitle">{{ formattedSelectedYear }}</p>
        </div>

        <div class="management-content">
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
              <select v-model="statusFilter" class="modern-select">
                <option value="all">Všetky stavy</option>
                <option value="published">Len publikované</option>
                <option value="draft">Len koncepty</option>
              </select>
            </div>
            <button @click="showAddSubpageForm" class="hero-btn primary">
              <span class="icon">+</span>
              Pridať článok
            </button>
          </div>

          <!-- Add Article Form -->
          <div v-if="showAddForm" class="form-section">
            <div class="form-card">
              <div class="modal-header">
                <h3>Pridať nový článok</h3>
                <button @click="cancelAdd" class="close-button">&times;</button>
              </div>

              <form @submit.prevent="saveSubpage" class="modal-form">
                <div class="form-group">
                  <label for="title">Názov *</label>
                  <input
                    id="title"
                    v-model="newArticle.title"
                    type="text"
                    placeholder="Zadajte názov článku"
                    class="modern-input"
                    required
                  />
                </div>

                <div class="form-group">
                  <label for="author_name">Meno autora *</label>
                  <input
                    id="author_name"
                    v-model="newArticle.author_name"
                    type="text"
                    placeholder="Zadajte meno autora"
                    class="modern-input"
                    required
                  />
                </div>

                <div class="form-group">
                  <label>Obsah</label>
                  <Editor
                    v-model="content"
                    :api-key="tinymceKey"
                    :init="tinymceConfig"
                  />
                </div>

                <div class="form-actions">
                  <button type="submit" :disabled="saving" class="hero-btn primary">
                    <span v-if="saving">Ukladanie...</span>
                    <span v-else>Uložiť článok</span>
                  </button>
                  <button type="button" @click="cancelAdd" class="hero-btn secondary">
                    Zrušiť
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Edit Article Form -->
          <div v-if="showEditForm && editingArticle" class="form-section">
            <div class="form-card">
              <div class="modal-header">
                <h3>Upraviť článok</h3>
                <button @click="cancelEdit" class="close-button">&times;</button>
              </div>

              <form @submit.prevent="updateSubpage" class="modal-form">
                <div class="form-group">
                  <label for="edit-title">Názov *</label>
                  <input
                    id="edit-title"
                    v-model="editingArticle.title"
                    type="text"
                    placeholder="Zadajte názov článku"
                    class="modern-input"
                    required
                  />
                </div>

                <div class="form-group">
                  <label for="edit-author_name">Meno autora *</label>
                  <input
                    id="edit-author_name"
                    v-model="editingArticle.author_name"
                    type="text"
                    placeholder="Zadajte meno autora"
                    class="modern-input"
                    required
                  />
                </div>

                <div class="form-group">
                  <label>Obsah</label>
                  <Editor
                    v-model="content"
                    :api-key="tinymceKey"
                    :init="tinymceConfig"
                  />
                </div>

                <div class="form-actions">
                  <button type="submit" :disabled="saving" class="hero-btn primary">
                    <span v-if="saving">Aktualizovanie...</span>
                    <span v-else>Aktualizovať článok</span>
                  </button>
                  <button type="button" @click="cancelEdit" class="hero-btn secondary">
                    Zrušiť
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Articles List -->
          <div class="articles-section">
            <div v-if="loading" class="loading-state">
              <p>Načítavanie článkov...</p>
            </div>

            <div v-else-if="filteredArticles.length === 0" class="empty-state">
              <div class="empty-icon">📄</div>
              <h3>Žiadne články</h3>
              <p v-if="searchQuery || statusFilter !== 'all'">
                Skúste upraviť vyhľadávanie alebo filter.
              </p>
              <p v-else>
                Pre tento ročník konferencie zatiaľ neboli vytvorené žiadne články.
              </p>
            </div>

            <div v-else class="cards-grid">
              <div
                v-for="article in filteredArticles"
                :key="article.id"
                class="feature-card article-card"
                :class="{ draft: !article.isPublished }"
              >
                <div class="card-header">
                  <h3>{{ article.title }}</h3>
                  <span class="status-badge" :class="{ active: article.isPublished }">
                    {{ article.isPublished ? 'Publikovaný' : 'Koncept' }}
                  </span>
                </div>

                <div class="card-content">
                  <p class="content-preview">{{ formatArticleSummary(article.content) }}</p>

                  <div class="card-meta">
                    <span class="meta-item">Vytvorené: {{ article.createdAt }}</span>
                    <span class="meta-item">Upravené: {{ article.updatedAt }}</span>
                  </div>
                </div>

                <div class="card-actions">
                  <button @click="editArticle(article)" class="action-btn edit">
                    Upraviť
                  </button>
                  <button @click="confirmDelete(article)" class="action-btn delete">
                    Zmazať
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="cancelDelete">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>Potvrdiť zmazanie</h3>
          <button @click="cancelDelete" class="close-button">&times;</button>
        </div>
        <div class="modal-form">
          <p v-if="deletingArticle">
            Ste si istí, že chcete zmazať "<strong>{{ deletingArticle.title }}</strong>"?
          </p>
          <p class="warning-text">Túto akciu nie je možné vrátiť späť.</p>

          <div class="form-actions">
            <button
              @click="deleteSubpage"
              :disabled="deleting"
              class="hero-btn primary delete-confirm"
            >
              <span v-if="deleting">Mazanie...</span>
              <span v-else>Zmazať článok</span>
            </button>
            <button @click="cancelDelete" class="hero-btn secondary">
              Zrušiť
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import Editor from '@tinymce/tinymce-vue'
import articleAPI from '@/services/article'
import conferenceAPI from '@/services/conferenceYear'
import adminPanel from '@/services/adminPanel'
import NavbarComponent from '@/components/NavbarComponent.vue'
import type { Settings } from 'tinymce'

export default defineComponent({
  name: 'EditDashboardView',
  components: {
    Editor,
    NavbarComponent
  },

  data() {
    return {
      // Editor's assigned academic years - will be loaded from API
      assignedYears: [] as any[],

      // Articles for the selected year
      articles: [] as any[],

      // Form states
      selectedYearId: null as number | null,
      showAddForm: false,
      showEditForm: false,
      showDeleteModal: false,
      editingArticle: null as any,
      deletingArticle: null as any,

      // New article form
      newArticle: {
        title: '',
        content: '',
        author_name: ''
      },

      // Loading states
      loading: false,
      saving: false,
      deleting: false,
      loadingYears: false,

      // Filter and search
      searchQuery: '',
      statusFilter: 'all' as 'all' | 'published' | 'draft',

      // Error handling
      error: null as string | null,

      // TinyMCE editor
      content: '' as string,
      tinymceKey: 'ama3uyd2ecm9bw4zvg1689uk4qkpcxzv7sxvjv47ylo35cen',
      tinymceConfig: {
        menubar: true,
        plugins: 'lists link image code',
        toolbar:
          'undo redo | bold italic underline | alignleft aligncenter | ' +
          'bullist numlist | image | code',
        height: 350,
        // API kľúč priamo v konfigurácii
        api_key: 'ama3uyd2ecm9bw4zvg1689uk4qkpcxzv7sxvjv47ylo35cen',
        automatic_uploads: true,
        images_upload_handler: function(
          blobInfo: any,
          success: (url: string) => void,
          failure: (errMsg: string) => void
        ) {
          const fd = new FormData()
          fd.append('file', blobInfo.blob(), blobInfo.filename())

          adminPanel.uploadImage(fd)
            .then(res => {
              success(res.data.location)
            })
            .catch(err => {
              console.error('Image upload error:', err)
              if (typeof failure === 'function') {
                failure(
                  'Image upload failed: ' +
                  (err.response?.data?.message || err.message || 'Unknown error')
                )
              }
            })
        }
      } as Settings
    }
  },

  computed: {
    // Convert API articles to subpage format for display
    filteredArticles(): any[] {
      if (!this.selectedYearId) return []

      let filtered = [...this.articles]

      // Apply search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(article =>
          article.title.toLowerCase().includes(query) ||
          (article.content && article.content.toLowerCase().includes(query))
        )
      }

      return filtered
    },

    selectedYear(): any {
      return this.assignedYears.find(year => year.id === this.selectedYearId)
    },

    formattedSelectedYear(): string {
      return this.selectedYear ? `${this.selectedYear.semester} ${this.selectedYear.year}` : ''
    }
  },

  methods: {
    // Helper methods
    getArticleSlug(article: any): string {
      return article.title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '')
    },

    formatDate(dateString: string): string {
      try {
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        })
      } catch (error) {
        return 'Invalid date'
      }
    },

    formatArticleSummary(content: string | null): string {
      if (!content) {
        return 'No content available...'
      }

      const maxLength = 150
      const cleanContent = content.replace(/<[^>]*>/g, '') // Strip HTML tags

      if (cleanContent.length <= maxLength) {
        return cleanContent
      }

      return cleanContent.substring(0, maxLength).trim() + '...'
    },

    validateArticle(articleData: any): string[] {
      const errors: string[] = []

      if (!articleData.title || articleData.title.trim().length === 0) {
        errors.push('Title is required')
      }

      if (articleData.title && articleData.title.length > 255) {
        errors.push('Title must be less than 255 characters')
      }

      if (!articleData.author_name || articleData.author_name.trim().length === 0) {
        errors.push('Author name is required')
      }

      if (articleData.author_name && articleData.author_name.length > 255) {
        errors.push('Author name must be less than 255 characters')
      }

      if (!articleData.conference_year_id) {
        errors.push('Conference year is required')
      }

      return errors
    },

    // Load conference years from API
    async loadConferenceYears() {
      this.loadingYears = true
      this.error = null

      try {
        const response = await conferenceAPI.getConferenceYears()
        this.assignedYears = response.data || []

        // Select first year if available
        if (this.assignedYears.length > 0) {
          this.selectedYearId = this.assignedYears[0].id
          await this.loadArticles()
        }
      } catch (error) {
        console.error('Error loading conference years:', error)
        this.error = 'Failed to load conference years'
      } finally {
        this.loadingYears = false
      }
    },

    // Year selection
    async selectYear(yearId: number) {
      this.selectedYearId = yearId
      this.showAddForm = false
      this.showEditForm = false
      await this.loadArticles()
    },

    // Load articles for selected year
    async loadArticles() {
      if (!this.selectedYearId) return

      this.loading = true
      this.error = null

      try {
        const response = await articleAPI.getArticlesByConferenceYear(this.selectedYearId)
        this.articles = response.data || []
      } catch (error) {
        console.error('Error loading articles:', error)
        this.error = 'Failed to load articles'
        this.articles = []
      } finally {
        this.loading = false
      }
    },

    // Add new article
    showAddSubpageForm() {
      this.showAddForm = true
      this.showEditForm = false
      this.newArticle = {
        title: '',
        content: '',
        author_name: ''
      }
      this.content = ''
    },

    async saveSubpage() {
      if (!this.newArticle.title.trim() || !this.selectedYearId) return

      // Validate the article data
      const createRequest = {
        title: this.newArticle.title,
        content: this.content,
        conference_year_id: this.selectedYearId,
        author_name: this.newArticle.author_name
      }

      const validationErrors = this.validateArticle(createRequest)
      if (validationErrors.length > 0) {
        this.error = validationErrors.join(', ')
        return
      }

      this.saving = true
      this.error = null

      try {
        const response = await articleAPI.createArticle(createRequest)

        if (response.data) {
          this.articles.push(response.data)
          this.showAddForm = false
          this.newArticle = { title: '', content: '', author_name: '' }
          this.content = ''

          if (response.message) {
            console.log('Success:', response.message)
          }
        }
      } catch (error) {
        console.error('Error creating article:', error)
        this.error = 'Failed to create article'
      } finally {
        this.saving = false
      }
    },

    // Edit article
    editArticle(article: any) {
      this.editingArticle = { ...article }
      this.content = article.content || ''
      this.showEditForm = true
      this.showAddForm = false
    },

    async updateSubpage() {
      if (!this.editingArticle || !this.editingArticle.title.trim()) return

      const updateRequest = {
        title: this.editingArticle.title,
        content: this.content,
        conference_year_id: this.editingArticle.conference_year_id,
        author_name: this.editingArticle.author_name
      }

      const validationErrors = this.validateArticle(updateRequest)
      if (validationErrors.length > 0) {
        this.error = validationErrors.join(', ')
        return
      }

      this.saving = true
      this.error = null

      try {
        const response = await articleAPI.updateArticle(this.editingArticle.id, updateRequest)

        if (response.data) {
          // Find and update the article in the array
          const index = this.articles.findIndex(a => a.id === this.editingArticle!.id)
          if (index !== -1) {
            this.articles[index] = response.data
          }

          this.showEditForm = false
          this.editingArticle = null
          this.content = ''

          if (response.message) {
            console.log('Success:', response.message)
          }
        }
      } catch (error) {
        console.error('Error updating article:', error)
        this.error = 'Failed to update article'
      } finally {
        this.saving = false
      }
    },

    // Delete article
    confirmDelete(article: any) {
      this.deletingArticle = article
      this.showDeleteModal = true
    },

    async deleteSubpage() {
      if (!this.deletingArticle) return

      this.deleting = true
      this.error = null

      try {
        const response = await articleAPI.deleteArticle(this.deletingArticle.id)

        // Remove from local array
        const index = this.articles.findIndex(a => a.id === this.deletingArticle!.id)
        if (index !== -1) {
          this.articles.splice(index, 1)
        }

        this.showDeleteModal = false
        this.deletingArticle = null

        if (response.message) {
          console.log('Success:', response.message)
        }
      } catch (error) {
        console.error('Error deleting article:', error)
        this.error = 'Failed to delete article'
      } finally {
        this.deleting = false
      }
    },

    // Cancel forms
    cancelAdd() {
      this.showAddForm = false
      this.newArticle = { title: '', content: '', author_name: '' }
      this.content = ''
    },

    cancelEdit() {
      this.showEditForm = false
      this.editingArticle = null
      this.content = ''
    },

    cancelDelete() {
      this.showDeleteModal = false
      this.deletingArticle = null
    },

    // Clear error
    clearError() {
      this.error = null
    }
  },

  async mounted() {
    await this.loadConferenceYears()
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
  display: flex;
  gap: 1rem;
}

/* Buttons */
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

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: var(--text-secondary);
  margin: 0;
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
