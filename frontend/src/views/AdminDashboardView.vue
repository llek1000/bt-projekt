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
            <span class="title-line">Admin</span>
            <span class="title-line highlight">Dashboard</span>
          </h1>
          <p class="hero-subtitle">
            Kompletná správa systému konferenčných ročníkov a používateľov
          </p>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">{{ totalArticles }}</span>
              <span class="stat-label">Články</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ users.length }}</span>
              <span class="stat-label">Používatelia</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ years.length }}</span>
              <span class="stat-label">Ročníky</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ adminFiles.length }}</span>
              <span class="stat-label">Súbory</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Existing sections... -->
    <!-- Year Management Section -->
    <section class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa ročníkov konferencie</h2>
          <p class="section-subtitle">Vytvárajte a spravujte konferenčné ročníky</p>
        </div>

        <div class="management-content">
          <div class="management-actions">
            <button @click="openYearModal()" class="btn primary">
              Nový ročník
            </button>
          </div>

          <div class="cards-grid">
            <div v-for="year in years" :key="year.id" class="feature-card">
              <div class="card-header">
                <h3>{{ formatConferenceYear(year) }}</h3>
                <div class="card-meta">
                  <div class="meta-item">
                    <span class="meta-label">Status:</span>
                    <span class="meta-value" :class="{ 'active': year.is_active }">
                      {{ year.is_active ? 'Aktívny' : 'Neaktívny' }}
                    </span>
                  </div>
                  <div class="meta-item">
                    <span class="meta-label">Vytvorené:</span>
                    <span class="meta-value">{{ formatDate(year.created_at) }}</span>
                  </div>
                </div>
              </div>
              
              <div class="card-actions">
                <button @click="openYearModal(year)" class="action-btn edit">
                  Upraviť
                </button>
                <button @click="deleteYear(year.id)" class="action-btn delete">
                  Vymazať
                </button>
              </div>
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
          <p class="section-subtitle">Nahrávajte a spravujte súbory pre systém</p>
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

        <div v-else-if="adminFiles.length === 0" class="empty-state">
          <div class="empty-icon">📂</div>
          <h3>Žiadne súbory</h3>
          <p>Zatiaľ nemáte nahrané žiadne súbory.</p>
        </div>

        <div v-else class="files-grid">
          <div v-for="file in adminFiles" :key="file.id" class="file-card">
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
              <button @click="deleteAdminFile(file.id)" class="action-btn delete">
                Vymazať
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- User Management Section -->
    <section class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa používateľov</h2>
          <p class="section-subtitle">Spravujte používateľov a ich oprávnenia</p>
        </div>

        <div class="management-content">
          <div class="management-actions">
            <div class="search-container">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Hľadať používateľov..."
                class="search-input"
              />
            </div>
            
            <button @click="showCreateUserModal = true" class="btn primary">
              Nový používateľ
            </button>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Meno</th>
                  <th>Email</th>
                  <th>Rola</th>
                  <th>Akcie</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in filteredUsers" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>{{ user.username }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span class="role-badge" :class="getRoleBadgeClass(user.roles?.[0]?.name || 'user')">
                      {{ user.roles?.[0]?.name || 'No Role' }}
                    </span>
                  </td>
                  <td class="actions-cell">
                    <button @click="openEditUserModal(user)" class="action-btn edit">
                      Upraviť
                    </button>
                    <button @click="selectedUser = user; showPasswordChangeModal = true" class="action-btn secondary">
                      Heslo
                    </button>
                    <button @click="selectedUser = user; showDeleteConfirmModal = true" class="action-btn delete">
                      Vymazať
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- Articles Management Section -->
    <section class="management-section alt-bg">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa článkov</h2>
          <p class="section-subtitle">Vytvárajte a spravujte články pre konferenčné ročníky</p>
        </div>

        <div class="management-content">
          <div class="management-actions">
            <div class="form-group">
              <label>Filter podľa ročníka:</label>
              <select v-model="selYearForArticles" class="modern-select">
                <option :value="null">Všetky ročníky</option>
                <option v-for="year in years" :key="year.id" :value="year.id">
                  {{ formatConferenceYear(year) }}
                </option>
              </select>
            </div>
            
            <button @click="openArticleModal()" class="btn primary">
              Nový článok
            </button>
          </div>

          <div class="cards-grid">
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
                <div class="content-preview" v-html="formatArticleSummary(article.content)"></div>
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
      </div>
    </section>

    <!-- Editor Assignments Section -->
    <section class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Priradenie editorov</h2>
          <p class="section-subtitle">Priraďte editorov k ročníkom konferencie</p>
        </div>

        <div class="management-content">
          <div class="management-actions">
            <div class="form-group">
              <label>Vybrať editora:</label>
              <select v-model="selEditor" class="modern-select">
                <option :value="null">Vyberte editora</option>
                <option v-for="editor in editors" :key="editor.id" :value="editor.id">
                  {{ editor.username }} ({{ editor.email }})
                </option>
              </select>
            </div>
            
            <div class="form-group">
              <label>Vybrať ročník:</label>
              <select v-model="selectedYearForAssignment" class="modern-select">
                <option :value="null">Vyberte ročník</option>
                <option v-for="year in years" :key="year.id" :value="year.id">
                  {{ formatConferenceYear(year) }}
                </option>
              </select>
            </div>
            
            <button @click="assignEditor" :disabled="!selEditor || !selectedYearForAssignment" class="btn primary">
              Priradiť editora
            </button>
          </div>

          <div class="assignments-list">
            <div v-for="assignment in assignments" :key="assignment.id" class="assignment-card">
              <div class="assignment-info">
                <h4>{{ assignment.user.username }}</h4>
                <p>{{ assignment.user.email }}</p>
                <span class="assignment-year">{{ formatConferenceYear(assignment.conference_year) }}</span>
              </div>
              <button @click="removeEditor(assignment.id)" class="action-btn delete">
                Odstrániť
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Modals -->
    <!-- Year Modal -->
    <div v-if="showYearForm" class="modal-overlay" @click.self="closeYearModal">
      <div class="modal-container">
        <div class="modal-header">
          <h3>{{ editYear ? 'Edit Conference Year' : 'Create Conference Year' }}</h3>
          <button @click="closeYearModal" class="close-button">&times;</button>
        </div>
        <form @submit.prevent="saveYear" class="modal-form">
          <div class="form-group">
            <label for="year">Year:</label>
            <input
              type="number"
              id="year"
              v-model="formYear.year"
              required
              min="2020"
              max="2030"
              class="modern-input"
            />
          </div>
          
          <div class="form-group">
            <label for="semester">Semester:</label>
            <select 
              id="semester"
              v-model="formYear.semester"
              required
              class="modern-select"
            >
              <option value="">Select Semester</option>
              <option value="Winter">Winter</option>
              <option value="Summer">Summer</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="checkbox-label">
              <input
                type="checkbox"
                v-model="formYear.is_active"
              />
              <span class="checkmark"></span>
              Active Conference Year
            </label>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="closeYearModal" class="btn secondary">
              Cancel
            </button>
            <button type="submit" class="btn primary">
              {{ editYear ? 'Update' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Article Modal -->
    <div v-if="showArticleForm" class="modal-overlay" @click="closeArticleModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>{{ editArticle ? 'Upraviť článok' : 'Nový článok' }}</h3>
          <button @click="closeArticleModal" class="close-button">&times;</button>
        </div>
        
        <form @submit.prevent="saveArticle" class="modal-form">
          <div class="form-group">
            <label for="articleTitle">Názov článku</label>
            <input
              id="articleTitle"
              type="text"
              v-model="formArticle.title"
              required
              class="modern-input"
            />
          </div>
          
          <div class="form-group">
            <label for="articleAuthor">Autor</label>
            <input
              id="articleAuthor"
              type="text"
              v-model="formArticle.author_name"
              required
              class="modern-input"
            />
          </div>
          
          <div class="form-group">
            <label for="articleConferenceYear">Konferenčný ročník</label>
            <select
              id="articleConferenceYear"
              v-model="formArticle.conference_year_id"
              required
              class="modern-select"
            >
              <option value="">Vyberte ročník</option>
              <option v-for="year in years" :key="year.id" :value="year.id">
                {{ formatConferenceYear(year) }}
              </option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="articleContent">Obsah</label>
            <Editor
              v-model="formArticle.content"
              :api-key="tinymceKey"
              :init="tinymceConfig"
            />
          </div>
          
          <div class="form-actions">
            <button type="button" @click="closeArticleModal" class="btn secondary">
              Zrušiť
            </button>
            <button type="submit" class="btn primary">
              {{ editArticle ? 'Aktualizovať' : 'Vytvoriť' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- User Modals -->
    <CreateUserModal
      v-if="showCreateUserModal"
      @close="showCreateUserModal = false"
      @user-created="handleUserCreated"
    />

    <EditUserModal
      v-if="showEditUserModal"
      @close="showEditUserModal = false"
      @user-updated="handleUserUpdated"
      @change-password="showPasswordChangeModal = true; showEditUserModal = false"
      @delete="showDeleteConfirmModal = true; showEditUserModal = false"
    />

    <PasswordChangeModal
      v-if="showPasswordChangeModal"
      @close="showPasswordChangeModal = false"
      @password-updated="handlePasswordUpdated"
    />

    <DeleteConfirmationModal
      v-if="showDeleteConfirmModal"
      @close="showDeleteConfirmModal = false"
      @confirm="handleUserDeleted"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, provide } from 'vue'
import Editor from '@tinymce/tinymce-vue'
import adminPanel from '@/services/adminPanel'
import NavbarComponent from '@/components/NavbarComponent.vue'
import CreateUserModal from '@/components/modals/CreateUserModal.vue'
import EditUserModal from '@/components/modals/EditUserModal.vue'
import PasswordChangeModal from '@/components/modals/PasswordChangeModal.vue'
import DeleteConfirmationModal from '@/components/modals/DeleteConfirmationModal.vue'

const years = ref<any[]>([])
const users = ref<any[]>([])
const roles = ref<any[]>([])
const articles = ref<any[]>([])

// File management
const adminFiles = ref<any[]>([])
const isLoadingFiles = ref(false)
const isDragOver = ref(false)

const selYearForArticles = ref<number|null>(null)

const showYearForm = ref(false)
const editYear = ref<any>(null)
const formYear = ref({ semester: '', year: '', is_active: false })

const showArticleForm = ref(false)
const editArticle = ref<any>(null)
const formArticle = ref({ title: '', content: '', author_name: '', conference_year_id: '' })

const searchQuery = ref('')
const showCreateUserModal = ref(false)
const showEditUserModal = ref(false)
const showDeleteConfirmModal = ref(false)
const showPasswordChangeModal = ref(false)
const selectedUser = ref<any>(null)

// Editor assignments
const editors = ref<any[]>([])
const assignments = ref<any[]>([])
const selEditor = ref<number|null>(null)
const selectedYearForAssignment = ref<number|null>(null)

// TinyMCE configuration with file upload support
const tinymceKey = 'ama3uyd2ecm9bw4zvg1689uk4qkpcxzv7sxvjv47ylo35cen'
const tinymceConfig = {
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
      const response = await adminPanel.uploadImage(formData)
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
            const response = await adminPanel.uploadFile(formData)
            callback(response.data.location, { text: file.name, title: file.name })
            await loadAdminFiles() // Refresh files list
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
            const response = await adminPanel.uploadImage(formData)
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
}

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user => 
    user.username.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  )
})

const filteredArticles = computed(() => {
  if (!selYearForArticles.value) return articles.value
  return articles.value.filter(article => 
    article.conference_year_id === selYearForArticles.value
  )
})

const totalArticles = computed(() => {
  return articles.value.length
})

// File management methods
async function loadAdminFiles() {
  isLoadingFiles.value = true
  try {
    const response = await adminPanel.getMyFiles()
    adminFiles.value = response.data.data || []
  } catch (error) {
    console.error('Error loading admin files:', error)
  } finally {
    isLoadingFiles.value = false
  }
}

function handleFileDrop(event: DragEvent) {
  event.preventDefault()
  isDragOver.value = false
  
  const files = event.dataTransfer?.files
  if (files) {
    uploadFiles(Array.from(files))
  }
}

function handleFileSelect(event: Event) {
  const target = event.target as HTMLInputElement
  const files = target.files
  if (files) {
    uploadFiles(Array.from(files))
  }
}

async function uploadFiles(files: File[]) {
  for (const file of files) {
    await uploadSingleFile(file)
  }
  await loadAdminFiles()
}

async function uploadSingleFile(file: File) {
  const formData = new FormData()
  formData.append('file', file)
  
  try {
    await adminPanel.uploadFile(formData)
  } catch (error) {
    console.error('Error uploading file:', error)
    alert(`Chyba pri nahrávaní súboru ${file.name}`)
  }
}

async function deleteAdminFile(fileId: number) {
  if (confirm('Naozaj chcete vymazať tento súbor?')) {
    try {
      await adminPanel.deleteFile(fileId)
      await loadAdminFiles()
    } catch (error) {
      console.error('Error deleting file:', error)
      alert('Chyba pri mazaní súboru')
    }
  }
}

function copyFileUrl(file: any) {
  navigator.clipboard.writeText(file.download_url).then(() => {
    alert('URL súboru bola skopírovaná do schránky')
  })
}

function isImageFile(file: any) {
  return file.mime_type?.startsWith('image/')
}

function isPdfFile(file: any) {
  return file.mime_type === 'application/pdf'
}

// Helper functions
function getRoleBadgeClass(role: string) {
  switch (role?.toLowerCase()) {
    case 'admin': return 'role-admin'
    case 'editor': return 'role-editor'
    default: return 'role-user'
  }
}

function formatDate(dateString?: string): string {
  if (!dateString) return 'Neznámy dátum'
  return new Date(dateString).toLocaleDateString('sk-SK')
}

function formatArticleSummary(content: string | null): string {
  if (!content) return 'Žiadny obsah'
  const cleanContent = content.replace(/<[^>]*>/g, '')
  return cleanContent.length > 100 
    ? cleanContent.substring(0, 100) + '...' 
    : cleanContent
}

function formatConferenceYear(year: any): string {
  if (!year) return 'Neznámy ročník'
  return `${year.semester} ${year.year}`
}

async function refreshUsers() {
  try {
    const response = await adminPanel.getUsers()
    users.value = response.data.users || []
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

async function fetchRoles() {
  try {
    const response = await adminPanel.getRoles()
    roles.value = response.data.roles || []
  } catch (error) {
    console.error('Error fetching roles:', error)
  }
}

async function refreshYears() {
  try {
    const response = await adminPanel.getConferenceYears()
    years.value = response.data.data || []
  } catch (error) {
    console.error('Error loading years:', error)
  }
}

async function refreshArticles() {
  try {
    const response = await adminPanel.listArticles()
    articles.value = response.data.data || []
  } catch (error) {
    console.error('Error loading articles:', error)
  }
}

async function refreshEditors() {
  try {
    const response = await adminPanel.getUsers()
    const allUsers = response.data.users || []
    editors.value = allUsers.filter(user => 
      user.roles?.some((role: any) => role.name.toLowerCase() === 'editor')
    )
  } catch (error) {
    console.error('Error loading editors:', error)
  }
}

async function refreshAssignments() {
  try {
    assignments.value = []
    for (const year of years.value) {
      try {
        const response = await adminPanel.listYearEditors(year.id)
        const yearAssignments = response.data.data || []
        assignments.value.push(...yearAssignments)
      } catch (error) {
        console.error(`Error loading assignments for year ${year.id}:`, error)
      }
    }
  } catch (error) {
    console.error('Error loading assignments:', error)
  }
}

// Year functions
function openYearModal(y = null) {
  editYear.value = y
  if (y) {
    formYear.value = {
      semester: y.semester,
      year: y.year,
      is_active: y.is_active
    }
  } else {
    formYear.value = { semester: '', year: '', is_active: false }
  }
  showYearForm.value = true
}

function closeYearModal() {
  showYearForm.value = false
  editYear.value = null
}

async function saveYear() {
  try {
    if (editYear.value) {
      await adminPanel.updateYear(editYear.value.id, formYear.value)
    } else {
      await adminPanel.createYear(formYear.value)
    }
    
    closeYearModal()
    await refreshYears()
  } catch (error) {
    console.error('Error saving year:', error)
    alert('Chyba pri ukladaní ročníka')
  }
}

async function deleteYear(id: number) {
  if (confirm('Naozaj chcete vymazať tento ročník?')) {
    try {
      await adminPanel.deleteYear(id)
      await refreshYears()
    } catch (error) {
      console.error('Error deleting year:', error)
      alert('Chyba pri mazaní ročníka')
    }
  }
}

async function deleteUser(id: number) {
  if (confirm('Naozaj chcete vymazať tohto používateľa?')) {
    try {
      await adminPanel.deleteUser(id)
      await refreshUsers()
    } catch (error) {
      console.error('Error deleting user:', error)
      alert('Chyba pri mazaní používateľa')
    }
  }
}

// Article functions
function openArticleModal(article = null) {
  editArticle.value = article
  if (article) {
    formArticle.value = {
      title: article.title,
      content: article.content || '',
      author_name: article.author_name,
      conference_year_id: article.conference_year_id
    }
  } else {
    formArticle.value = {
      title: '',
      content: '',
      author_name: '',
      conference_year_id: ''
    }
  }
  showArticleForm.value = true
}

function closeArticleModal() {
  showArticleForm.value = false
  editArticle.value = null
}

async function saveArticle() {
  try {
    if (editArticle.value) {
      await adminPanel.updateArticle(editArticle.value.id, formArticle.value)
    } else {
      await adminPanel.createArticle(formArticle.value)
    }
    
    closeArticleModal()
    await refreshArticles()
  } catch (error) {
    console.error('Error saving article:', error)
    alert('Chyba pri ukladaní článku')
  }
}

async function deleteArticle(id: number) {
  if (confirm('Naozaj chcete vymazať tento článok?')) {
    try {
      await adminPanel.deleteArticle(id)
      await refreshArticles()
    } catch (error) {
      console.error('Error deleting article:', error)
      alert('Chyba pri mazaní článku')
    }
  }
}

// Assignment functions
async function assignEditor() {
  if (!selEditor.value || !selectedYearForAssignment.value) return
  
  try {
    await adminPanel.assignEditor(selectedYearForAssignment.value, selEditor.value)
    selEditor.value = null
    selectedYearForAssignment.value = null
    await refreshAssignments()
  } catch (error) {
    console.error('Error assigning editor:', error)
    alert('Chyba pri priraďovaní editora')
  }
}

async function removeEditor(assignmentId: number) {
  if (confirm('Naozaj chcete odstrániť toto priradenie?')) {
    try {
      const assignment = assignments.value.find(a => a.id === assignmentId)
      if (assignment) {
        await adminPanel.removeEditor(assignment.conference_year_id, assignmentId)
        await refreshAssignments()
      }
    } catch (error) {
      console.error('Error removing editor:', error)
      alert('Chyba pri odstraňovaní priradenia')
    }
  }
}

// User modal handlers
function openEditUserModal(user: any) {
  selectedUser.value = user
  showEditUserModal.value = true
}

function handleUserCreated() {
  showCreateUserModal.value = false
  refreshUsers()
}

function handleUserUpdated() {
  showEditUserModal.value = false
  refreshUsers()
}

function handleUserDeleted() {
  showDeleteConfirmModal.value = false
  refreshUsers()
}

function handlePasswordUpdated() {
  showPasswordChangeModal.value = false
}

// Provide functions for child components
provide('selectedUser', () => selectedUser.value)
provide('roles', () => roles.value)
provide('adminPanel', adminPanel)

onMounted(async () => {
  await Promise.all([
    refreshUsers(),
    fetchRoles(),
    refreshYears(),
    refreshArticles(),
    refreshEditors(),
    refreshAssignments(),
    loadAdminFiles()
  ])
})
</script>

<style scoped>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

/* CSS Variables */
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

/* Base Styles */
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

.management-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
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

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 25px;
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
  color: bl;
  border: 1px solid black;
  border-radius: 25px;
}

.btn.primary:hover {
  background: #1d4ed8;
  border: none;
}

.btn.secondary {
  background: var(--text-secondary);
  color: black;
}

.btn.secondary:hover {
  background: #4b5563;
  transform: translateY(-1px);
}

/* Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.feature-card {
  background: var(--white);
  border-radius: 25px;
  padding: 1.5rem;
  box-shadow: var(--shadow-md);
  transition: all 0.3s ease;
  border: 1px solid black;
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

.meta-value.active {
  color: var(--success-color);
  font-weight: 600;
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
  color: black;
  border: 1px solid black;
  border-radius: 25px;
}

.action-btn.edit:hover {
  background: #1d4ed8;
  border: none;
}

.action-btn.delete {
  background: var(--danger-color);
  color: black;
  border: 1px solid black;
  border-radius: 25px;
}

.action-btn.delete:hover {
  background: #dc2626;
  border: none;
}

.action-btn.download {
  background: var(--success-color);
  color: black;
  border: 1px solid black;
  border-radius: 25px;
}

.action-btn.download:hover {
  background: #059669;
}

.action-btn.copy {
  background: var(--accent-color);
  color: black;
  border: 1px solid black;
  border-radius: 25px;
}

.action-btn.copy:hover {
  background: #d97706;
}

.action-btn.secondary {
  background: var(--text-secondary);
  color: black;
  border: 1px solid black;
  border-radius: 25px;
}

.action-btn.secondary:hover {
  background: #4b5563;
  border: none ;
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

/* Table */
.table-container {
  background: var(--white);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th,
.data-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

.data-table th {
  background: var(--secondary-color);
  font-weight: 600;
  color: var(--text-primary);
}

.actions-cell {
  white-space: nowrap;
}

.actions-cell .action-btn {
  margin-right: 0.5rem;
}

/* Role Badge */
.role-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.role-badge.role-admin {
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
}

.role-badge.role-editor {
  background: rgba(37, 99, 235, 0.1);
  color: #2563eb;
}

.role-badge.role-user {
  background: rgba(107, 114, 128, 0.1);
  color: #6b7280;
}

/* Form Controls */
.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.modern-select, .modern-input {
  padding: 0.75rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  width: 100%;
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

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

/* Assignment Cards */
.assignments-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
  margin-top: 2rem;
}

.assignment-card {
  background: var(--white);
  border-radius: 8px;
  padding: 1rem;
  box-shadow: var(--shadow-md);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.assignment-info h4 {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.assignment-info p {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin-bottom: 0.5rem;
}

.assignment-year {
  font-size: 0.8rem;
  background: var(--primary-color);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-weight: 500;
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
  border-radius: 12px;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
  background-color: white;
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

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

/* Year Modal Styles */
.year-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  

}

.year-modal-content {
  background-color: black;
  border-radius: 16px;
  width: 500px;
  max-width: 90%;
  max-height: 90vh;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

@keyframes modalSlideIn {
  from {
    transform: translateY(-50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.year-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 25px;
  border-bottom: 1px solid black;
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: black;
  border-radius: 16px 16px 0 0;
}

.year-modal-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: black;
}

.year-modal-close {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: black;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.year-modal-close:hover {
  background: rgba(255, 255, 255, 0.3);
}

.year-modal-body {
  padding: 25px;
}

.year-form-group {
  margin-bottom: 20px;
}

.year-form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: var(--text-primary);
  font-size: 14px;
}

.year-form-input,
.year-form-select {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid black;
  border-radius: 25px;
  font-size: 14px;
  transition: all 0.2s ease;
  background: black;
}

.year-form-input:focus,
.year-form-select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.year-checkbox-group {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 20px;
}

.year-checkbox {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.year-checkbox-label {
  font-weight: 500;
  color: var(--text-primary);
  cursor: pointer;
}

.year-form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

.year-btn {
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s ease;
  font-size: 14px;
}

.year-btn-save {
  background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
  color: black;
}

.year-btn-save:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.year-btn-cancel {
  background: black;
  color: var(--text-secondary);
  border: 1px solid #000000;
}

.year-btn-cancel:hover {
  background: black;
  color: var(--text-primary);
}

/* Dark overlay animation */
.year-modal::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Responsive design */
@media (max-width: 768px) {
  .year-modal-content {
    width: 95%;
    margin: 1rem;
  }
  
  .year-modal-header {
    padding: 15px 20px;
  }
  
  .year-modal-body {
    padding: 20px;
  }
  
  .year-form-actions {
    flex-direction: column;
  }
  
  .year-btn {
    width: 100%;
  }
}
</style>