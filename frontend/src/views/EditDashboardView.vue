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
              <button @click="$refs.fileInput.click()" class="btn secondary">
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
                <span class="file-date">{{ formatDate(file.created_at) }}</span>
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
      // Articles
      articles: [] as any[],
      assignments: [] as any[],
      filteredArticles: [] as any[],
      searchQuery: '',
      selectedConferenceYear: '',
      isLoading: false,
      
      // Files
      files: [] as any[],
      isLoadingFiles: false,
      isDragOver: false,
      
      // Article Modal
      showArticleModal: false,
      editingArticle: null as any,
      articleForm: {
        title: '',
        content: '',
        author_name: '',
        conference_year_id: ''
      },
      
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
      } as Settings
    }
  },

  computed: {
    totalArticles() {
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
    async loadAssignments() {
      try {
        const response = await editorPanel.getMyAssignments()
        this.assignments = response.data.data || []
      } catch (error) {
        console.error('Error loading assignments:', error)
      }
    },

    async loadArticles() {
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

    async loadFiles() {
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

    filterArticles() {
      let filtered = [...this.articles]

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(article => 
          article.title.toLowerCase().includes(query) ||
          article.author_name.toLowerCase().includes(query)
        )
      }

      if (this.selectedConferenceYear) {
        filtered = filtered.filter(article => 
          article.conference_year_id == this.selectedConferenceYear
        )
      }

      // Filter by user's assignments
      const assignedYearIds = this.assignments.map(a => a.conference_year_id)
      filtered = filtered.filter(article => 
        assignedYearIds.includes(article.conference_year_id)
      )

      this.filteredArticles = filtered
    },

    openArticleModal(article = null) {
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

    closeArticleModal() {
      this.showArticleModal = false
      this.editingArticle = null
    },

    async saveArticle() {
      try {
        if (this.editingArticle) {
          await editorPanel.updateArticle(this.editingArticle.id, this.articleForm)
        } else {
          await editorPanel.createArticle(this.articleForm)
        }
        
        this.closeArticleModal()
        await this.loadArticles()
      } catch (error) {
        console.error('Error saving article:', error)
        alert('Chyba pri ukladaní článku')
      }
    },

    async deleteArticle(id: number) {
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
    handleFileDrop(event: DragEvent) {
      event.preventDefault()
      this.isDragOver = false
      
      const files = event.dataTransfer?.files
      if (files) {
        this.uploadFiles(Array.from(files))
      }
    },

    handleFileSelect(event: Event) {
      const target = event.target as HTMLInputElement
      const files = target.files
      if (files) {
        this.uploadFiles(Array.from(files))
      }
    },

    async uploadFiles(files: File[]) {
      for (const file of files) {
        await this.uploadSingleFile(file)
      }
      await this.loadFiles()
    },

    async uploadSingleFile(file: File) {
      const formData = new FormData()
      formData.append('file', file)
      
      try {
        await editorPanel.uploadFile(formData)
      } catch (error) {
        console.error('Error uploading file:', error)
        alert(`Chyba pri nahrávaní súboru ${file.name}`)
      }
    },

    async deleteFile(fileId: number) {
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

    copyFileUrl(file: any) {
      navigator.clipboard.writeText(file.download_url).then(() => {
        alert('URL súboru bola skopírovaná do schránky')
      })
    },

    isImageFile(file: any) {
      return file.mime_type?.startsWith('image/')
    },

    isPdfFile(file: any) {
      return file.mime_type === 'application/pdf'
    },

    // Utility methods
    formatConferenceYear(conferenceYear: any) {
      if (!conferenceYear) return 'Neznámy ročník'
      return `${conferenceYear.semester} ${conferenceYear.year}`
    },

    formatDate(dateString: string) {
      return editorPanel.helpers.formatDate(dateString)
    },

    getContentPreview(content: string) {
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
    transform: translateY(0px) rotate(0deg);
    opacity: 1;
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
    opacity: 0.8;
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
}

.management-section.alt-bg {
  background-color: var(--white);
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
  max-width: 600px;
  margin: 0 auto;
}

.management-actions {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
  align-items: flex-end;
  flex-wrap: wrap;
}

.search-container {
  flex: 1;
  min-width: 300px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 0.95rem;
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
  align-items: flex-end;
}

.filter-controls .form-group {
  margin-bottom: 0;
}

.filter-controls label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-secondary);
  margin-bottom: 0.5rem;
}

.btn {
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

.btn.primary {
  background: var(--primary-color);
  color: white;
}

.btn.primary:hover {
  background: #1d4ed8;
  transform: translateY(-1px);
}

.btn.secondary {
  background: var(--text-secondary);
  color: white;
}

.btn.secondary:hover {
  background: #4b5563;
  transform: translateY(-1px);
}

/* Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.feature-card {
  background: var(--white);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: var(--shadow-md);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.card-header {
  margin-bottom: 1rem;
}

.feature-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.75rem;
  line-height: 1.4;
}

.card-meta {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.meta-item {
  display: flex;
  font-size: 0.875rem;
}

.meta-label {
  font-weight: 500;
  color: var(--text-secondary);
  min-width: 80px;
}

.meta-value {
  color: var(--text-primary);
}

.card-content {
  margin-bottom: 1.5rem;
}

.content-preview {
  color: var(--text-secondary);
  line-height: 1.5;
  font-size: 0.9rem;
}

.card-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

.action-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.action-btn.edit {
  background: var(--primary-color);
  color: white;
}

.action-btn.edit:hover {
  background: #1d4ed8;
}

.action-btn.delete {
  background: var(--danger-color);
  color: white;
}

.action-btn.delete:hover {
  background: #dc2626;
}

.action-btn.download {
  background: var(--success-color);
  color: white;
}

.action-btn.download:hover {
  background: #059669;
}

.action-btn.copy {
  background: var(--accent-color);
  color: white;
}

.action-btn.copy:hover {
  background: #d97706;
}

/* File Upload Area */
.file-upload-area {
  border: 2px dashed var(--border-color);
  border-radius: 12px;
  padding: 3rem;
  text-align: center;
  transition: all 0.3s ease;
  cursor: pointer;
  margin-bottom: 2rem;
}

.file-upload-area:hover,
.file-upload-area.drag-over {
  border-color: var(--primary-color);
  background-color: rgba(37, 99, 235, 0.05);
}

.upload-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.upload-icon {
  font-size: 3rem;
  opacity: 0.6;
}

.upload-content h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
}

.upload-content p {
  color: var(--text-secondary);
  margin-bottom: 1rem;
}

/* Files Grid */
.files-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
  margin-top: 2rem;
}

.file-card {
  background: var(--white);
  border-radius: 8px;
  padding: 1rem;
  box-shadow: var(--shadow-md);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s ease;
}

.file-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.file-icon {
  font-size: 2rem;
  flex-shrink: 0;
}

.file-info {
  flex: 1;
  min-width: 0;
}

.file-info h4 {
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.file-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.8rem;
  color: var(--text-secondary);
}

.file-actions {
  display: flex;
  gap: 0.5rem;
  flex-shrink: 0;
}

/* Form Controls */
.modern-select, .modern-input {
  padding: 0.75rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.modern-select {
  background-color: var(--white);
  cursor: pointer;
}

.modern-input:focus, .modern-select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Loading and Empty States */
.loading-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
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
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.6;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
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
  background: var(--white);
  border-radius: 12px;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.modal-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
}

.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--text-secondary);
  padding: 0.25rem;
}

.close-button:hover {
  color: var(--text-primary);
}

.modal-form {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
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
  
  .management-actions {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-controls {
    flex-direction: column;
  }
  
  .cards-grid {
    grid-template-columns: 1fr;
  }
  
  .files-grid {
    grid-template-columns: 1fr;
  }
  
  .file-actions {
    flex-direction: column;
  }
}
</style>
