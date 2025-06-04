<template>
  <div class="dashboard-container">
    <NavbarComponent />

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="hero-particles">
          <div class="particle" v-for="i in 20" :key="i"
               :style="{ left: Math.random() * 100 + '%', animationDelay: Math.random() * 6 + 's' }"></div>
        </div>
      </div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">
            <span class="title-line">Editor</span>
            <span class="title-line highlight">Dashboard</span>
          </h1>
          <p class="hero-subtitle">
            Správa článkov a obsahu pre konferenčné ročníky
          </p>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">{{ totalArticles }}</span>
              <span class="stat-label">Moje Články</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ assignments.length }}</span>
              <span class="stat-label">Priradené Ročníky</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ files.length }}</span>
              <span class="stat-label">Nahrané Súbory</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Articles Management Section -->
    <section class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa Článkov</h2>
          <p class="section-subtitle">Vytvárajte a upravujte články pre vámi spravované konferenčné ročníky</p>
        </div>

        <div class="management-actions">
          <div class="search-container">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Hľadať články..."
              class="search-input"
            />
          </div>

          <div class="filter-controls">
            <div class="form-group">
              <label>Filter podľa ročníka:</label>
              <select v-model="selectedConferenceYear" class="modern-select">
                <option value="">Všetky ročníky</option>
                <option v-for="assignment in assignments" :key="assignment.id" :value="assignment.conference_year_id">
                  {{ formatConferenceYear(assignment.conference_year) }}
                </option>
              </select>
            </div>
          </div>

          <button @click="openArticleModal()" class="btn primary">
            Nový Článok
          </button>
        </div>

        <!-- Articles Grid -->
        <div v-if="isLoading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Načítavam články...</p>
        </div>

        <div v-else-if="filteredArticles.length === 0" class="empty-state">
          <div class="empty-icon">📄</div>
          <h3>Žiadne články</h3>
          <p>Zatiaľ nemáte vytvorené žiadne články pre vaše priradené ročníky.</p>
        </div>

        <div v-else class="cards-grid">
          <div v-for="article in filteredArticles" :key="article.id" class="feature-card">
            <div class="card-header">
              <h3>{{ article.title }}</h3>
              <div class="card-meta">
                <div class="meta-item">
                  <span class="meta-label">Autor:</span>
                  <span class="meta-value">{{ article.author_name }}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Ročník:</span>
                  <span class="meta-value">{{ formatConferenceYear(article.conference_year) }}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Vytvorené:</span>
                  <span class="meta-value">{{ formatDate(article.created_at) }}</span>
                </div>
              </div>
            </div>

            <div class="card-content">
              <div class="content-preview" v-html="getContentPreview(article.content)"></div>
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

    <!-- File Management Section -->
    <section class="management-section alt-bg">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa Súborov</h2>
          <p class="section-subtitle">Nahrávajte a spravujte súbory pre vaše články</p>
        </div>

        <div class="management-actions">
          <div class="file-upload-area"
               @drop="handleFileDrop"
               @dragover.prevent
               @dragenter.prevent
               :class="{ 'drag-over': isDragOver }">
            <input
              type="file"
              ref="fileInput"
              @change="handleFileSelect"
              multiple
              accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif"
              style="display: none;"
            />
            <div class="upload-content">
              <div class="upload-icon">📁</div>
              <h3>Nahrať súbory</h3>
              <p>Pretiahnite súbory sem alebo kliknite pre výber</p>
              <button @click="triggerFileInput" class="btn secondary">
                Vybrať súbory
              </button>
            </div>
          </div>
        </div>

        <!-- Files Grid -->
        <div v-if="isLoadingFiles" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Načítavam súbory...</p>
        </div>

        <div v-else-if="files.length === 0" class="empty-state">
          <div class="empty-icon">📂</div>
          <h3>Žiadne súbory</h3>
          <p>Zatiaľ nemáte nahrané žiadne súbory.</p>
        </div>

        <div v-else class="files-grid">
          <div v-for="file in files" :key="file.id" class="file-card">
            <div class="file-icon">
              <span v-if="isImageFile(file)">🖼️</span>
              <span v-else-if="isPdfFile(file)">📄</span>
              <span v-else>📎</span>
            </div>

            <div class="file-info">
              <h4>{{ file.original_name }}</h4>
              <div class="file-meta">
                <span class="file-size">{{ file.file_size_human }}</span>
                <span class="file-date">{{ formatDate(file.created_at || '') }}</span>
              </div>
            </div>

            <div class="file-actions">
              <a :href="file.download_url" target="_blank" class="action-btn download">
                Stiahnuť
              </a>
              <button @click="copyFileUrl(file)" class="action-btn copy">
                Kopírovať URL
              </button>
              <button @click="deleteFile(file.id)" class="action-btn delete">
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
          <h3>{{ editingArticle ? 'Upraviť článok' : 'Nový článok' }}</h3>
          <button @click="closeArticleModal" class="close-button">&times;</button>
        </div>

        <form @submit.prevent="saveArticle" class="modal-form">
          <div class="form-group">
            <label for="articleTitle">Názov článku</label>
            <input
              id="articleTitle"
              type="text"
              v-model="articleForm.title"
              required
              class="modern-input"
            />
          </div>

          <div class="form-group">
            <label for="articleAuthor">Autor</label>
            <input
              id="articleAuthor"
              type="text"
              v-model="articleForm.author_name"
              required
              class="modern-input"
            />
          </div>

          <div class="form-group">
            <label for="articleConferenceYear">Konferenčný ročník</label>
            <select
              id="articleConferenceYear"
              v-model="articleForm.conference_year_id"
              required
              class="modern-select"
            >
              <option value="">Vyberte ročník</option>
              <option v-for="assignment in assignments" :key="assignment.id" :value="assignment.conference_year_id">
                {{ formatConferenceYear(assignment.conference_year) }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="articleContent">Obsah</label>
            <Editor
              v-model="articleForm.content"
              :api-key="tinymceKey"
              :init="tinymceConfig"
            />
          </div>

          <div class="form-actions">
            <button type="button" @click="closeArticleModal" class="btn secondary">
              Zrušiť
            </button>
            <button type="submit" class="btn primary">
              {{ editingArticle ? 'Aktualizovať' : 'Vytvoriť' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import Editor from '@tinymce/tinymce-vue'
import editorPanel from '@/services/editorPanel'
import NavbarComponent from '@/components/NavbarComponent.vue'

interface Article {
  id: number
  title: string
  content?: string
  author_name: string
  conference_year_id: number
  conference_year?: {
    id: number
    year: string
    semester: string
    is_active: boolean
  }
  created_at?: string
  updated_at?: string
}

interface Assignment {
  id: number
  user_id: number
  conference_year_id: number
  conference_year?: {
    id: number
    year: string
    semester: string
    is_active: boolean
  }
  created_at?: string
  updated_at?: string
}

interface FileData {
  id: number
  name: string
  original_name: string
  file_path: string
  mime_type: string
  file_size: number
  file_size_human?: string
  category?: string
  download_url: string
  created_at?: string
}

interface ArticleForm {
  title: string
  content: string
  author_name: string
  conference_year_id: string | number
}

// Define TinyMCE config interface locally
interface TinyMCEConfig {
  height?: number
  menubar?: boolean | string
  plugins?: string[]
  toolbar?: string
  content_style?: string
  automatic_uploads?: boolean
  file_picker_types?: string
  images_upload_handler?: (blobInfo: any) => Promise<string>
  file_picker_callback?: (callback: any, value: any, meta: any) => void
}

export default defineComponent({
  name: 'EditDashboardView',
  components: {
    NavbarComponent,
    Editor
  },

  setup() {
    // Define template refs
    const fileInput = ref<HTMLInputElement | null>(null)

    return {
      fileInput
    }
  },

  data() {
    return {
      // Articles
      articles: [] as Article[],
      assignments: [] as Assignment[],
      filteredArticles: [] as Article[],
      searchQuery: '',
      selectedConferenceYear: '' as string | number,
      isLoading: false,

      // Files
      files: [] as FileData[],
      isLoadingFiles: false,
      isDragOver: false,

      // Article Modal
      showArticleModal: false,
      editingArticle: null as Article | null,
      articleForm: {
        title: '',
        content: '',
        author_name: '',
        conference_year_id: ''
      } as ArticleForm,

      // TinyMCE configuration with file upload support
      tinymceKey: 'ama3uyd2ecm9bw4zvg1689uk4qkpcxzv7sxvjv47ylo35cen',
      tinymceConfig: {
        height: 400,
        menubar: true,
        plugins: [
          'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
          'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
          'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help | image media link',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',

        // File upload configuration
        automatic_uploads: true,
        file_picker_types: 'file image media',

        // Image upload
        images_upload_handler: async (blobInfo: any) => {
          const formData = new FormData()
          formData.append('file', blobInfo.blob(), blobInfo.filename())

          try {
            const response = await editorPanel.uploadImage(formData)
            return response.data.location
          } catch (error) {
            console.error('Image upload failed:', error)
            throw error
          }
        },

        // File upload handler
        file_picker_callback: async (callback: any, value: any, meta: any) => {
          if (meta.filetype === 'file') {
            const input = document.createElement('input')
            input.setAttribute('type', 'file')
            input.setAttribute('accept', '.pdf,.doc,.docx,.txt,.zip,.rar')

            input.onchange = async () => {
              const file = input.files?.[0]
              if (file) {
                const formData = new FormData()
                formData.append('file', file)

                try {
                  const response = await editorPanel.uploadFile(formData)
                  callback(response.data.location, { text: file.name, title: file.name })
                  await this.loadFiles() // Refresh files list
                } catch (error) {
                  console.error('File upload failed:', error)
                  alert('Nahrávanie súboru zlyhalo')
                }
              }
            }

            input.click()
          } else if (meta.filetype === 'image') {
            const input = document.createElement('input')
            input.setAttribute('type', 'file')
            input.setAttribute('accept', 'image/*')

            input.onchange = async () => {
              const file = input.files?.[0]
              if (file) {
                const formData = new FormData()
                formData.append('file', file)

                try {
                  const response = await editorPanel.uploadImage(formData)
                  callback(response.data.location, { alt: file.name, title: file.name })
                } catch (error) {
                  console.error('Image upload failed:', error)
                  alert('Nahrávanie obrázka zlyhalo')
                }
              }
            }

            input.click()
          }
        }
      } as TinyMCEConfig
    }
  },

  computed: {
    totalArticles(): number {
      return this.articles.length
    }
  },

  watch: {
    searchQuery() {
      this.filterArticles()
    },
    selectedConferenceYear() {
      this.filterArticles()
    }
  },

  methods: {
    // Articles methods
    async loadAssignments(): Promise<void> {
      try {
        const response = await editorPanel.getMyAssignments()
        this.assignments = response.data.data || []
      } catch (error) {
        console.error('Error loading assignments:', error)
      }
    },

    async loadArticles(): Promise<void> {
      this.isLoading = true
      try {
        const response = await editorPanel.listArticles()
        this.articles = response.data.data || []
        this.filterArticles()
      } catch (error) {
        console.error('Error loading articles:', error)
      } finally {
        this.isLoading = false
      }
    },

    async loadFiles(): Promise<void> {
      this.isLoadingFiles = true
      try {
        const response = await editorPanel.getMyFiles()
        this.files = response.data.data || []
      } catch (error) {
        console.error('Error loading files:', error)
      } finally {
        this.isLoadingFiles = false
      }
    },

    filterArticles(): void {
      let filtered = [...this.articles]

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(article =>
          article.title.toLowerCase().includes(query) ||
          article.author_name.toLowerCase().includes(query)
        )
      }

      if (this.selectedConferenceYear) {
        const selectedYearId = Number(this.selectedConferenceYear)
        filtered = filtered.filter(article =>
          article.conference_year_id === selectedYearId
        )
      }

      // Filter by user's assignments
      const assignedYearIds = this.assignments.map(a => a.conference_year_id)
      filtered = filtered.filter(article =>
        assignedYearIds.includes(article.conference_year_id)
      )

      this.filteredArticles = filtered
    },

    openArticleModal(article: Article | null = null): void {
      this.editingArticle = article
      if (article) {
        this.articleForm = {
          title: article.title,
          content: article.content || '',
          author_name: article.author_name,
          conference_year_id: article.conference_year_id
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

    closeArticleModal(): void {
      this.showArticleModal = false
      this.editingArticle = null
    },

    async saveArticle(): Promise<void> {
      try {
        // Create proper request object with type conversion
        const requestData = {
          title: this.articleForm.title,
          content: this.articleForm.content,
          author_name: this.articleForm.author_name,
          conference_year_id: Number(this.articleForm.conference_year_id)
        }

        if (this.editingArticle) {
          await editorPanel.updateArticle(this.editingArticle.id, requestData)
        } else {
          await editorPanel.createArticle(requestData)
        }

        this.closeArticleModal()
        await this.loadArticles()
      } catch (error) {
        console.error('Error saving article:', error)
        alert('Chyba pri ukladaní článku')
      }
    },

    async deleteArticle(id: number): Promise<void> {
      if (confirm('Naozaj chcete vymazať tento článok?')) {
        try {
          await editorPanel.deleteArticle(id)
          await this.loadArticles()
        } catch (error) {
          console.error('Error deleting article:', error)
          alert('Chyba pri mazaní článku')
        }
      }
    },

    // File methods
    triggerFileInput(): void {
      // Now this will work correctly
      this.fileInput?.click()
    },

    handleFileDrop(event: DragEvent): void {
      event.preventDefault()
      this.isDragOver = false

      const files = event.dataTransfer?.files
      if (files) {
        this.uploadFiles(Array.from(files))
      }
    },

    handleFileSelect(event: Event): void {
      const target = event.target as HTMLInputElement
      const files = target.files
      if (files) {
        this.uploadFiles(Array.from(files))
      }
    },

    async uploadFiles(files: File[]): Promise<void> {
      for (const file of files) {
        await this.uploadSingleFile(file)
      }
      await this.loadFiles()
    },

    async uploadSingleFile(file: File): Promise<void> {
      const formData = new FormData()
      formData.append('file', file)

      try {
        await editorPanel.uploadFile(formData)
      } catch (error) {
        console.error('Error uploading file:', error)
        alert(`Chyba pri nahrávaní súboru ${file.name}`)
      }
    },

    async deleteFile(fileId: number): Promise<void> {
      if (confirm('Naozaj chcete vymazať tento súbor?')) {
        try {
          await editorPanel.deleteFile(fileId)
          await this.loadFiles()
        } catch (error) {
          console.error('Error deleting file:', error)
          alert('Chyba pri mazaní súboru')
        }
      }
    },

    copyFileUrl(file: FileData): void {
      navigator.clipboard.writeText(file.download_url).then(() => {
        alert('URL súboru bola skopírovaná do schránky')
      })
    },

    isImageFile(file: FileData): boolean {
      return file.mime_type?.startsWith('image/') || false
    },

    isPdfFile(file: FileData): boolean {
      return file.mime_type === 'application/pdf'
    },

    // Utility methods
    formatConferenceYear(conferenceYear: any): string {
      if (!conferenceYear) return 'Neznámy ročník'
      return `${conferenceYear.semester} ${conferenceYear.year}`
    },

    formatDate(dateString?: string): string {
      // Handle undefined/null values
      if (!dateString) return 'Neznámy dátum'
      return editorPanel.helpers.formatDate(dateString)
    },

    getContentPreview(content?: string): string {
      // Handle undefined content
      if (!content) return 'Žiadny obsah...'
      return editorPanel.helpers.getContentPreview(content, 100)
    }
  },

  async mounted() {
    await Promise.all([
      this.loadAssignments(),
      this.loadArticles(),
      this.loadFiles()
    ])
  }
})
</script>

<style scoped>
/* CSS Variables */
:root {
  --primary-color: #3b82f6;
  --primary-dark: #1e40af;
  --primary-light: #60a5fa;
  --secondary-color: #6b7280;
  --success-color: #10b981;
  --warning-color: #f59e0b;
  --danger-color: #ef4444;
  --info-color: #06b6d4;
  
  --text-primary: #111827;
  --text-secondary: #6b7280;
  --text-light: #9ca3af;
  --text-white: #ffffff;
  
  --bg-primary: #ffffff;
  --bg-secondary: #f8fafc;
  --bg-tertiary: #f1f5f9;
  --bg-dark: #1f2937;
  
  --border-color: #e5e7eb;
  --border-light: #f3f4f6;
  --border-dark: #d1d5db;
  
  --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  
  --radius-xs: 4px;
  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-2xl: 24px;
  --radius-full: 9999px;
  
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --transition-fast: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Reset & Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dashboard-container {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: var(--text-primary);
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  min-height: 100vh;
  line-height: 1.6;
  font-size: 16px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
  font-weight: 700;
  line-height: 1.25;
  color: var(--text-primary);
}

p {
  color: var(--text-secondary);
  line-height: 1.6;
}

/* Hero Section */
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
  padding: 8rem 0 5rem;
  color: var(--text-white);
  overflow: hidden;
  min-height: 70vh;
  display: flex;
  align-items: center;
}

.hero-background {
  position: absolute;
  inset: 0;
  z-index: 1;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.2) 100%);
  z-index: 2;
}

.hero-particles {
  position: absolute;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 3;
}

.particle {
  position: absolute;
  width: 6px;
  height: 6px;
  background: rgba(255, 255, 255, 0.7);
  border-radius: var(--radius-full);
  animation: float 8s ease-in-out infinite;
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
}

@keyframes float {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg) scale(1); 
    opacity: 0.7;
  }
  33% { 
    transform: translateY(-30px) rotate(120deg) scale(1.2); 
    opacity: 1;
  }
  66% { 
    transform: translateY(-15px) rotate(240deg) scale(0.8); 
    opacity: 0.8;
  }
}

.hero-content {
  position: relative;
  z-index: 4;
  text-align: center;
  max-width: 900px;
  margin: 0 auto;
}

.hero-title {
  font-size: clamp(2.5rem, 6vw, 4.5rem);
  font-weight: 800;
  margin-bottom: 2rem;
  line-height: 1.1;
  letter-spacing: -0.02em;
}

.title-line {
  display: block;
  animation: slideInUp 0.8s ease-out;
}

.title-line.highlight {
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #fbbf24 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: slideInUp 0.8s ease-out 0.2s both;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hero-subtitle {
  font-size: clamp(1.125rem, 2.5vw, 1.5rem);
  margin-bottom: 4rem;
  opacity: 0.95;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
  font-weight: 400;
  line-height: 1.6;
  animation: slideInUp 0.8s ease-out 0.4s both;
}

.hero-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 2rem;
  max-width: 900px;
  margin: 0 auto;
  animation: slideInUp 0.8s ease-out 0.6s both;
}

.stat-item {
  text-align: center;
  padding: 2rem 1.5rem;
  background: rgba(255, 255, 255, 0.12);
  border-radius: var(--radius-2xl);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: var(--transition);
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.stat-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  transition: left 0.6s ease;
}

.stat-item:hover::before {
  left: 100%;
}

.stat-item:hover {
  transform: translateY(-8px) scale(1.02);
  background: rgba(255, 255, 255, 0.18);
  box-shadow: var(--shadow-xl);
}

.stat-number {
  display: block;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 800;
  color: #fbbf24;
  margin-bottom: 0.75rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-label {
  display: block;
  font-size: 0.875rem;
  opacity: 0.9;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Management Section */
.management-section {
  padding: 6rem 0;
  position: relative;
}

.management-section.alt-bg {
  background: var(--bg-primary);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.section-title {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  letter-spacing: -0.02em;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -0.5rem;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
  border-radius: var(--radius-full);
}

.section-subtitle {
  font-size: 1.25rem;
  color: var(--text-secondary);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}

.management-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
  gap: 1.5rem;
  flex-wrap: wrap;
  padding: 2rem;
  background: var(--bg-primary);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--border-light);
}

.search-container {
  flex: 1;
  max-width: 400px;
  min-width: 250px;
}

.search-input {
  width: 100%;
  padding: 1rem 1.25rem;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-xl);
  font-size: 1rem;
  background: var(--bg-secondary);
  transition: var(--transition);
  font-weight: 500;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  background: var(--bg-primary);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  transform: translateY(-1px);
}

.filter-controls {
  display: flex;
  gap: 1rem;
  align-items: flex-end;
}

.filter-controls .form-group {
  margin-bottom: 0;
}

.filter-controls label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: 0.5rem;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 1rem 2rem;
  border-radius: var(--radius-xl);
  font-weight: 600;
  font-size: 0.875rem;
  text-decoration: none;
  cursor: pointer;
  border: none;
  transition: var(--transition);
  white-space: nowrap;
  position: relative;
  overflow: hidden;
  min-height: 48px;
  letter-spacing: 0.025em;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.btn:hover::before {
  left: 100%;
}

.btn.primary {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: var(--text-white);
  box-shadow: var(--shadow-lg);
}

.btn.primary:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-xl);
  background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
}

.btn.secondary {
  background: linear-gradient(135deg, var(--secondary-color) 0%, #4b5563 100%);
  color: var(--text-white);
  box-shadow: var(--shadow-md);
}

.btn.secondary:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
}

/* Grids */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 2rem;
  margin-top: 3rem;
}

.files-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 3rem;
}

/* Cards */
.feature-card {
  background: var(--bg-primary);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--border-light);
  transition: var(--transition);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  height: 100%;
  position: relative;
}

.feature-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.feature-card:hover::before {
  transform: scaleX(1);
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-2xl);
  border-color: var(--border-dark);
}

.card-header {
  padding: 2rem;
  border-bottom: 1px solid var(--border-light);
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
}

.feature-card h3 {
  margin: 0 0 0.75rem 0;
  color: var(--text-primary);
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1.3;
}

.card-meta {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.meta-item {
  display: flex;
  align-items: center;
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
}

.meta-label {
  font-weight: 600;
  color: var(--text-secondary);
  min-width: 80px;
}

.meta-value {
  color: var(--text-primary);
}

.card-content {
  padding: 2rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.content-preview {
  flex: 1;
  margin-bottom: 1.5rem;
  padding: 1.5rem;
  background: var(--bg-secondary);
  border-radius: var(--radius-lg);
  border-left: 4px solid var(--primary-color);
  font-size: 0.875rem;
  line-height: 1.6;
  color: var(--text-secondary);
  max-height: 120px;
  overflow: hidden;
  position: relative;
}

.content-preview::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 30px;
  background: linear-gradient(transparent, var(--bg-secondary));
}

.card-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid var(--border-light);
}

.action-btn {
  padding: 0.75rem 1.5rem;
  border-radius: var(--radius-lg);
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  border: none;
  transition: var(--transition);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.action-btn.edit {
  background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
  color: var(--text-white);
}

.action-btn.edit:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.action-btn.delete {
  background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
  color: var(--text-white);
}

.action-btn.delete:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.action-btn.download {
  background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
  color: var(--text-white);
}

.action-btn.download:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.action-btn.copy {
  background: linear-gradient(135deg, var(--info-color) 0%, #0891b2 100%);
  color: var(--text-white);
}

.action-btn.copy:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

/* File Upload */
.file-upload-area {
  border: 3px dashed var(--border-color);
  border-radius: var(--radius-2xl);
  padding: 4rem 2rem;
  text-align: center;
  background: var(--bg-secondary);
  transition: var(--transition);
  cursor: pointer;
  position: relative;
  overflow: hidden;
  margin-bottom: 3rem;
}

.file-upload-area::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.file-upload-area:hover::before,
.file-upload-area.drag-over::before {
  opacity: 1;
}

.file-upload-area:hover,
.file-upload-area.drag-over {
  border-color: var(--primary-color);
  background: var(--bg-primary);
  transform: translateY(-2px);
}

.upload-content {
  position: relative;
  z-index: 1;
}

.upload-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
  opacity: 0.6;
  color: var(--primary-color);
}

.upload-content h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.upload-content p {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin-bottom: 1.5rem;
}

/* File Cards */
.file-card {
  background: var(--bg-primary);
  border-radius: var(--radius-xl);
  padding: 1.5rem;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-light);
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.file-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary-color);
}

.file-icon {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-lg);
  background: linear-gradient(135deg, var(--info-color) 0%, #0891b2 100%);
  color: var(--text-white);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
  box-shadow: var(--shadow-md);
}

.file-info {
  flex: 1;
  min-width: 0;
}

.file-info h4 {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.file-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.file-actions {
  display: flex;
  gap: 0.5rem;
  flex-shrink: 0;
}

/* Form Controls */
.modern-input,
.modern-select {
  width: 100%;
  padding: 1.25rem 1.5rem;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-xl);
  font-size: 1rem;
  background: var(--bg-secondary);
  transition: var(--transition);
  font-weight: 500;
  font-family: inherit;
}

.modern-input:focus,
.modern-select:focus {
  outline: none;
  border-color: var(--primary-color);
  background: var(--bg-primary);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  transform: translateY(-1px);
}

.modern-select {
  cursor: pointer;
}

/* Loading and Empty States */
.loading-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 6rem 3rem;
  text-align: center;
  background: var(--bg-primary);
  border-radius: var(--radius-2xl);
  border: 2px dashed var(--border-color);
  margin-top: 3rem;
  position: relative;
  overflow: hidden;
}

.loading-state::before,
.empty-state::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.02) 0%, rgba(139, 92, 246, 0.02) 100%);
}

.loading-spinner {
  width: 48px;
  height: 48px;
  border: 4px solid var(--border-light);
  border-top: 4px solid var(--primary-color);
  border-radius: var(--radius-full);
  animation: spin 1s linear infinite;
  margin-bottom: 2rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 6rem;
  margin-bottom: 2rem;
  opacity: 0.4;
  color: var(--text-light);
}

.empty-state h3 {
  margin: 0 0 1rem 0;
  color: var(--text-primary);
  font-size: 1.5rem;
  font-weight: 700;
}

.empty-state p {
  color: var(--text-secondary);
  margin: 0 0 2rem 0;
  max-width: 500px;
  font-size: 1rem;
  line-height: 1.6;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(12px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease;
  padding: 1rem;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-container {
  background: white;
  border-radius: var(--radius-2xl);
  width: 90vw;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-2xl);
  border: 1px solid var(--border-light);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from { 
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to { 
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 2.5rem;
  border-bottom: 1px solid var(--border-light);
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
}

.modal-header h3 {
  margin: 0;
  font-size: 1.5rem;
  color: var(--text-primary);
  font-weight: 700;
}

.close-button {
  background: var(--bg-primary);
  border: 2px solid var(--border-color);
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--text-secondary);
  padding: 0.75rem;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-full);
  transition: var(--transition);
}

.close-button:hover {
  background: var(--danger-color);
  color: var(--text-white);
  border-color: var(--danger-color);
  transform: scale(1.1);
}

.modal-form {
  padding: 2.5rem;
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 2rem;
}

.form-group label {
  display: block;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-light);
  margin-top: auto;
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .management-actions {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-container {
    max-width: none;
    width: 100%;
  }
  
  .filter-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .cards-grid,
  .files-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-container {
    width: 95vw;
    max-height: 95vh;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .file-actions {
    flex-direction: column;
  }
  
  .hero-stats {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}
</style>
