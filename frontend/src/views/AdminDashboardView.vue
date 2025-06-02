<template>
  <div class="dashboard-container">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="hero-particles">
          <div class="particle" v-for="i in 50" :key="i"></div>
        </div>
      </div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">
            <span class="title-line">Admin</span>
            <span class="title-line highlight">Dashboard</span>
          </h1>
          <p class="hero-subtitle">
            Kompletná správa systému a obsahu
          </p>
          <div class="hero-stats">
            <div class="stat-item">
              <div class="stat-number">{{ users.length }}</div>
              <div class="stat-label">Používateľov</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">{{ years.length }}</div>
              <div class="stat-label">Ročníkov</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">{{ totalArticles }}</div>
              <div class="stat-label">Článkov</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Users Management -->
    <section class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa používateľov</h2>
          <p class="section-subtitle">Spravujte používateľov a ich role</p>
        </div>
        <div class="management-content">
          <div class="management-actions">
            <button @click="showCreateUserModal = true" class="hero-btn primary">
              <i class="icon-plus">+</i>
              Vytvoriť používateľa
            </button>
            <div class="search-container">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Hľadať používateľov..."
                class="search-input"
              />
            </div>
          </div>
          
          <div class="table-container">
            <table class="modern-table">
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
                <tr v-for="user in filteredUsers" :key="user.id" class="table-row">
                  <td>{{ user.id }}</td>
                  <td>{{ user.username }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span class="role-badge" :class="getRoleBadgeClass(user.roles?.[0]?.name || 'user')">
                      {{ user.roles?.[0]?.name || 'No Role' }}
                    </span>
                  </td>
                  <td>
                    <div class="card-actions">
                      <button @click="openEditUserModal(user)" class="action-btn edit">
                        Upraviť
                      </button>
                      <button @click="deleteUser(user.id)" class="action-btn delete">
                        Vymazať
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- Conference Years Management -->
    <section class="management-section alt-bg">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa ročníkov konferencie</h2>
          <p class="section-subtitle">Spravujte ročníky a semestre konferencie</p>
        </div>
        <div class="management-content">
          <div class="management-actions">
            <button @click="openYearModal()" class="hero-btn primary">
              <i class="icon-plus">+</i>
              Pridať ročník
            </button>
          </div>
          
          <div class="cards-grid">
            <div v-for="year in years" :key="year.id" 
                 class="feature-card year-card" 
                 :class="{ active: year.is_active }">
              <div class="card-header">
                <div class="year-info">
                  <h3>{{ year.semester }} {{ year.year }}</h3>
                  <span class="status-badge" :class="{ active: year.is_active }">
                    {{ year.is_active ? 'Aktívny' : 'Neaktívny' }}
                  </span>
                </div>
              </div>
              <div class="card-content">
                <div class="year-meta">
                  <div class="meta-item">
                    <span class="meta-label">Vytvorený:</span>
                    <span class="meta-value">{{ formatDate(year.created_at) }}</span>
                  </div>
                  <div class="meta-item">
                    <span class="meta-label">Stav:</span>
                    <span class="meta-value">{{ year.is_active ? 'Aktívny' : 'Neaktívny' }}</span>
                  </div>
                </div>
              </div>
              <div class="card-actions">
                <button @click="openYearModal(year)" class="action-btn edit">
                  <i class="icon-edit">✏️</i>
                  Upraviť
                </button>
                <button @click="deleteYear(year.id)" class="action-btn delete">
                  <i class="icon-delete">🗑️</i>
                  Vymazať
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Articles Management -->
    <section class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa článkov</h2>
          <p class="section-subtitle">Spravujte články pre jednotlivé ročníky</p>
        </div>
        <div class="management-content">
          <div class="management-actions">
            <select v-model="selYearForArticles" @change="refreshArticles" class="modern-select">
              <option :value="null">Vyberte ročník</option>
              <option v-for="year in years" :key="year.id" :value="year.id">
                {{ year.semester }} {{ year.year }}
              </option>
            </select>
            <button @click="openArticleModal()" class="hero-btn primary" :disabled="!selYearForArticles">
              <i class="icon-plus">+</i>
              Pridať článok
            </button>
          </div>
          
          <div v-if="selYearForArticles" class="cards-grid">
            <div v-for="article in articles" :key="article.id" class="feature-card article-card">
              <div class="card-header">
                <h3>{{ article.title }}</h3>
              </div>
              <div class="card-content">
                <div class="article-meta">
                  <div class="meta-item">
                    <span class="meta-label">Autor:</span>
                    <span class="meta-value">{{ article.author_name }}</span>
                  </div>
                </div>
                <div class="content-preview" v-html="formatArticleSummary(article.content)"></div>
              </div>
              <div class="card-meta">
                <div class="meta-item">
                  <span class="meta-label">Vytvorený:</span>
                  <span class="meta-value">{{ formatDate(article.created_at) }}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">Upravený:</span>
                  <span class="meta-value">{{ formatDate(article.updated_at) }}</span>
                </div>
              </div>
              <div class="card-actions">
                <button @click="openArticleModal(article)" class="action-btn edit">
                  <i class="icon-edit">✏️</i>
                  Upraviť
                </button>
                <button @click="deleteArticle(article.id)" class="action-btn delete">
                  <i class="icon-delete">🗑️</i>
                  Vymazať
                </button>
              </div>
            </div>
          </div>
          
          <div v-else class="empty-state">
            <div class="empty-icon">📄</div>
            <h3>Vyberte ročník</h3>
            <p>Vyberte ročník konferencie pre zobrazenie článkov</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Year Modal -->
    <div v-if="showYearForm" class="modal-overlay" @click.self="closeYearModal">
      <div class="modal-container large">
        <div class="modal-header">
          <h3>{{ editYear ? 'Upraviť ročník' : 'Pridať ročník' }}</h3>
          <button @click="closeYearModal" class="hero-btn secondary">×</button>
        </div>
        <form @submit.prevent="saveYear" class="modal-form">
          <!-- Základné údaje ročníka -->
          <div class="form-section">
            <h4 class="section-title">Základné údaje</h4>
            <div class="form-group">
              <label>Rok:</label>
              <input v-model="formYear.year" type="text" required class="modern-input" placeholder="napr. 2024" />
            </div>
            <div class="form-group">
              <label>Semester:</label>
              <select v-model="formYear.semester" required class="modern-input">
                <option value="">Vyberte semester</option>
                <option value="Winter">Winter</option>
                <option value="Summer">Summer</option>
              </select>
            </div>
            <div class="form-group checkbox-group">
              <label class="checkbox-label">
                <input v-model="formYear.is_active" type="checkbox" />
                <span class="checkmark"></span>
                Aktívny ročník
              </label>
            </div>
          </div>

          <!-- Sekcia pre editorov (len pri úprave existujúceho ročníka) -->
          <div v-if="editYear" class="form-section">
            <h4 class="section-title">Priradení editori</h4>
            
            <!-- Pridanie nového editora -->
            <div class="editor-assignment-controls">
              <div class="select-group">
                <label>Vybrať editora:</label>
                <select v-model="selEditor" class="modern-input">
                  <option :value="null">Vyberte editora</option>
                  <option v-for="editor in editors" :key="editor.id" :value="editor.id">
                    {{ editor.username }}
                  </option>
                </select>
              </div>
              <button 
                type="button" 
                @click="assignEditor" 
                class="hero-btn primary" 
                :disabled="!selEditor"
              >
                <i class="icon-plus">+</i>
                Priradiť editora
              </button>
            </div>

            <!-- Zoznam priradených editorov -->
            <div class="assignments-list">
              <div v-if="assignments.length === 0" class="empty-assignments">
                <p>Žiadni editori nie sú priradení k tomuto ročníku.</p>
              </div>
              <div v-else>
                <div v-for="assignment in assignments" :key="assignment.id" class="assignment-item">
                  <div class="assignment-info">
                    <div class="editor-name">
                      <i class="icon-user">👤</i>
                      {{ assignment.user?.username }}
                    </div>
                    <div class="assignment-meta">
                      <span class="assignment-date">
                        Priradený: {{ formatDate(assignment.created_at) }}
                      </span>
                    </div>
                  </div>
                  <button 
                    type="button" 
                    @click="removeEditor(assignment.id)" 
                    class="action-btn delete small"
                    title="Odstrániť editora"
                  >
                    <i class="icon-delete">🗑️</i>
                    Odstrániť
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeYearModal" class="hero-btn secondary">Zrušiť</button>
            <button type="submit" class="hero-btn primary">{{ editYear ? 'Uložiť' : 'Vytvoriť' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Article Modal -->
    <div v-if="showArticleForm" class="modal-overlay" @click.self="closeArticleModal">
      <div class="modal-container large">
        <div class="modal-header">
          <h3>{{ editArticle ? 'Upraviť článok' : 'Pridať článok' }}</h3>
          <button @click="closeArticleModal" class="hero-btn secondary">×</button>
        </div>
        <form @submit.prevent="saveArticle" class="modal-form">
          <div class="form-group">
            <label>Názov:</label>
            <input v-model="formArticle.title" type="text" required class="modern-input" placeholder="Názov článku" />
          </div>
          <div class="form-group">
            <label>Autor:</label>
            <input v-model="formArticle.author_name" type="text" required class="modern-input" placeholder="Meno autora" />
          </div>
          <div class="form-group">
            <label>Obsah:</label>
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

    <!-- Modals -->
    <CreateUserModal
      v-if="showCreateUserModal"
      @close="showCreateUserModal = false"
      @user-created="handleUserCreated"
    />
    
    <EditUserModal
      v-if="showEditUserModal"
      @close="showEditUserModal = false"
      @user-updated="handleUserUpdated"
      @change-password="showPasswordChangeModal = true"
      @delete="showDeleteConfirmModal = true"
    />
    
    <DeleteConfirmationModal
      v-if="showDeleteConfirmModal"
      @close="showDeleteConfirmModal = false"
      @confirm="handleUserDeleted"
    />
    
    <PasswordChangeModal
      v-if="showPasswordChangeModal"
      @close="showPasswordChangeModal = false"
      @password-updated="handlePasswordUpdated"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, provide } from 'vue'
import Editor from '@tinymce/tinymce-vue'
import adminPanel from '@/services/adminPanel'
import CreateUserModal from '@/components/modals/CreateUserModal.vue'
import EditUserModal from '@/components/modals/EditUserModal.vue'
import DeleteConfirmationModal from '@/components/modals/DeleteConfirmationModal.vue'
import PasswordChangeModal from '@/components/modals/PasswordChangeModal.vue'

const years = ref<any[]>([])
const users = ref<any[]>([])
const roles = ref<any[]>([])
const articles = ref<any[]>([])

const selYearForArticles = ref<number|null>(null)

const showYearForm = ref(false)
const editYear = ref<any>(null)
const formYear = ref({ semester: '', year: '', is_active: false })

const showArticleForm = ref(false)
const editArticle = ref<any>(null)
const formArticle = ref({ title: '', content: '', author_name: '' })

const searchQuery = ref('')
const showCreateUserModal = ref(false)
const showEditUserModal = ref(false)
const showDeleteConfirmModal = ref(false)
const showPasswordChangeModal = ref(false)
const selectedUser = ref<any>(null)

// Pridané refs pre editor assignments
const editors = ref<any[]>([])
const assignments = ref<any[]>([])
const selEditor = ref<number|null>(null)

// TinyMCE configuration
const tinymceKey = 'ama3uyd2ecm9bw4zvg1689uk4qkpcxzv7sxvjv47ylo35cen'
const tinymceConfig = {
  menubar: true,
  plugins: 'lists link image code',
  toolbar: 'undo redo | bold italic underline | alignleft aligncenter | bullist numlist | image | code',
  height: 350,
  api_key: tinymceKey
}

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user => 
    user.username?.toLowerCase().includes(query) || 
    user.email?.toLowerCase().includes(query)
  )
})

const totalArticles = computed(() => {
  return articles.value.length
})

// Helper functions
function getRoleBadgeClass(role: string) {
  switch (role?.toLowerCase()) {
    case 'admin': return 'role-admin'
    case 'editor': return 'role-editor'
    default: return 'role-default'
  }
}

function formatDate(dateString?: string): string {
  if (!dateString) return 'N/A'
  try {
    return new Date(dateString).toLocaleDateString('sk-SK')
  } catch {
    return 'N/A'
  }
}

function formatArticleSummary(content: string | null): string {
  if (!content) return 'Žiadny obsah...'
  const cleanContent = content.replace(/<[^>]*>/g, '')
  return cleanContent.length > 150 ? cleanContent.substring(0, 150) + '...' : cleanContent
}

async function refreshUsers() {
  try {
    const response = await adminPanel.getUsers()
    users.value = response.data?.users || []
  } catch (error) {
    console.error('Error loading users:', error)
  }
}

async function fetchRoles() {
  try {
    const response = await adminPanel.getRoles()
    roles.value = response.data?.roles || []
  } catch (error) {
    console.error('Error loading roles:', error)
  }
}

async function refreshYears() {
  try {
    const response = await adminPanel.getConferenceYears()
    years.value = response.data?.data || []
  } catch (error) {
    console.error('Error loading years:', error)
  }
}

async function refreshArticles() {
  if (!selYearForArticles.value) return
  try {
    const response = await adminPanel.listArticles({ conference_year_id: selYearForArticles.value })
    articles.value = response.data?.data || []
  } catch (error) {
    console.error('Error loading articles:', error)
  }
}

async function refreshEditors() {
  try {
    const response = await adminPanel.getUsers()
    editors.value = (response.data?.users || []).filter((user: any) => 
      user.roles?.some((role: any) => role.name.toLowerCase() === 'editor')
    )
  } catch (error) {
    console.error('Error loading editors:', error)
  }
}

async function refreshAssignments() {
  if (!editYear.value?.id) return
  try {
    const response = await adminPanel.listYearEditors(editYear.value.id)
    assignments.value = response.data || []
  } catch (error) {
    console.error('Error loading assignments:', error)
  }
}

// Year functions
function openYearModal(y = null) {
  editYear.value = y
  if (y) {
    formYear.value = { ...y }
    // Načítaj priradenia editorov pre tento ročník
    refreshAssignments()
  } else {
    formYear.value = { semester: '', year: '', is_active: false }
    assignments.value = []
  }
  selEditor.value = null
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
    refreshYears()
  } catch (error) {
    console.error('Error saving year:', error)
    alert('Chyba pri ukladaní ročníka: ' + (error.response?.data?.message || error.message))
  }
}

async function deleteYear(id: number) {
  if (confirm('Naozaj chcete vymazať tento ročník?')) {
    try {
      await adminPanel.deleteYear(id)
      refreshYears()
    } catch (error) {
      console.error('Error deleting year:', error)
      alert('Chyba pri mazaní ročníka: ' + (error.response?.data?.message || error.message))
    }
  }
}

async function deleteUser(id: number) {
  if (confirm('Naozaj chcete vymazať tohoto používateľa?')) {
    try {
      await adminPanel.deleteUser(id)
      refreshUsers()
    } catch (error) {
      console.error('Error deleting user:', error)
      alert('Chyba pri mazaní používateľa: ' + (error.response?.data?.message || error.message))
    }
  }
}

// Article functions
function openArticleModal(article = null) {
  editArticle.value = article
  if (article) {
    formArticle.value = { ...article }
  } else {
    formArticle.value = { title: '', content: '', author_name: '' }
  }
  showArticleForm.value = true
}

function closeArticleModal() {
  showArticleForm.value = false
  editArticle.value = null
}

async function saveArticle() {
  try {
    const articleData = {
      ...formArticle.value,
      conference_year_id: selYearForArticles.value
    }
    
    if (editArticle.value) {
      await adminPanel.updateArticle(editArticle.value.id, articleData)
    } else {
      await adminPanel.createArticle(articleData)
    }
    closeArticleModal()
    refreshArticles()
  } catch (error) {
    console.error('Error saving article:', error)
    alert('Chyba pri ukladaní článku: ' + (error.response?.data?.message || error.message))
  }
}

async function deleteArticle(id: number) {
  if (confirm('Naozaj chcete vymazať tento článok?')) {
    try {
      await adminPanel.deleteArticle(id)
      refreshArticles()
    } catch (error) {
      console.error('Error deleting article:', error)
      alert('Chyba pri mazaní článku: ' + (error.response?.data?.message || error.message))
    }
  }
}

// Assignment functions
async function assignEditor() {
  if (!editYear.value?.id || !selEditor.value) return
  try {
    await adminPanel.assignEditor(editYear.value.id, selEditor.value)
    selEditor.value = null
    refreshAssignments()
  } catch (error) {
    console.error('Error assigning editor:', error)
    alert('Chyba pri priraďovaní editora: ' + (error.response?.data?.message || error.message))
  }
}

async function removeEditor(assignmentId: number) {
  if (!editYear.value?.id) return
  try {
    await adminPanel.removeEditor(editYear.value.id, assignmentId)
    refreshAssignments()
  } catch (error) {
    console.error('Error removing editor:', error)
    alert('Chyba pri odstraňovaní editora: ' + (error.response?.data?.message || error.message))
  }
}

// User modal handlers
function openEditUserModal(user: any) { 
  selectedUser.value = user
  showEditUserModal.value = true 
}

function handleUserCreated() { 
  refreshUsers()
  showCreateUserModal.value = false 
}

function handleUserUpdated() { 
  refreshUsers()
  showEditUserModal.value = false 
}

function handleUserDeleted() { 
  refreshUsers()
  showDeleteConfirmModal.value = false
  showEditUserModal.value = false 
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
    refreshEditors() // Pridané načítanie editorov
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

/* Hero Section */
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 4rem 0;
  overflow: hidden;
  color: white;
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
  0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 1; }
  50% { transform: translateY(-20px) rotate(180deg); opacity: 0.8; }
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
  background: linear-gradient(45deg, #f59e0b, #eab308);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 1.3rem;
  margin-bottom: 2.5rem;
  opacity: 0.9;
}

.hero-stats {
  display: flex;
  justify-content: center;
  gap: 3rem;
  margin-top: 2.5rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 700;
  color: #f59e0b;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 1rem;
  opacity: 0.8;
}

/* Management Sections */
.management-section {
  padding: 4rem 0;
  background: white;
}

.management-section.alt-bg {
  background: var(--light-bg);
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
  font-size: 1.2rem;
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
  gap: 1rem;
}

.search-container {
  flex: 1;
  max-width: 300px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #333; /* výrazný border */
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  color: white;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Buttons */
.hero-btn {
  display: inline-flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  cursor: pointer;
  border: none;
  gap: 0.5rem;
}

.hero-btn.primary {
  background: var(--primary-color);
  color: black;
  box-shadow: var(--shadow-md);
  border: 2px solid black;
}

.hero-btn.primary:hover:not(:disabled) {
  background: #1d4ed8;
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border: none;
}

.hero-btn.primary:disabled {
  background: var(--text-muted);
  cursor: not-allowed;
  transform: none;
}

.hero-btn.secondary {
  background: var(--border-color);
  color: var(--text-primary);
}

.hero-btn.secondary:hover {
  background: #d1d5db;
}

.icon-plus, .icon-edit, .icon-delete {
  font-size: 1rem;
}

/* Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.feature-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  position: relative;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.feature-card.year-card {
  border-left: 4px solid #dc3545; /* červený border pre neaktívne */
  background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); /* červenkastý gradient pre neaktívne */
  border: 2px solid #dc3545;
}

.feature-card.year-card.active {
  border-left-color: var(--success-color);
  background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); /* zelenkastý gradient pre aktívne */
  border: 2px solid var(--success-color);
}

.feature-card.year-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #721c24; /* tmavo červená pre neaktívne */
  margin: 0;
}

.feature-card.year-card.active h3 {
  color: #155724; /* tmavo zelená pre aktívne */
}

.feature-card.year-card .meta-label {
  font-weight: 600;
  color: #721c24; /* tmavo červená pre neaktívne labels */
}

.feature-card.year-card.active .meta-label {
  color: #155724; /* tmavo zelená pre aktívne labels */
}

.feature-card.year-card .meta-value {
  color: #721c24; /* tmavo červená pre neaktívne hodnoty */
}

.feature-card.year-card.active .meta-value {
  color: #155724; /* tmavo zelená pre aktívne hodnoty */
}

.feature-card.year-card .status-badge {
  background: #dc3545; /* červené pozadie pre neaktívne */
  color: white;
}

.feature-card.year-card.active .status-badge.active {
  background: var(--success-color); /* zelené pozadie pre aktívne */
  color: white;
}

.card-content {
  margin-bottom: 1rem;
}

.year-meta, .article-meta {
  margin-bottom: 1rem;
}

.meta-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.meta-label {
  font-weight: 600;
  color: var(--text-secondary);
}

.meta-value {
  color: var(--text-primary);
}

.content-preview {
  color: var(--text-secondary);
  font-size: 0.9rem;
  line-height: 1.5;
  margin-top: 0.5rem;
}

.card-meta {
  border-top: 1px solid var(--border-color);
  padding-top: 1rem;
  margin-top: 1rem;
}

/* Action Buttons */
.action-btn {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  gap: 0.25rem;
  color: black;
}

.action-btn.edit {
  background: var(--info-color);
  color: black;
}

.action-btn.edit:hover {
  background: #2563eb;
}

.action-btn.delete {
  background: var(--danger-color);
  color: black;
}

.action-btn.delete:hover {
  background: #dc2626;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
  margin-top: 1rem;
}

/* Select Controls */
.modern-select {
  padding: 0.75rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  background: white;
  transition: all 0.3s ease;
  min-width: 200px;
}

.modern-select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Empty States */
.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: var(--text-secondary);
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: var(--text-primary);
}

.empty-state p {
  font-size: 1rem;
}

/* Table */
.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
}

.modern-table th {
  background: var(--light-bg);
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: var(--text-primary);
  border-bottom: 1px solid var(--border-color);
}

.modern-table td {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.table-row:hover {
  background: var(--light-bg);
}

.role-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.role-admin {
  background-color: #dc3545;
  color: white;
}

.role-editor {
  background-color: #28a745;
  color: white;
}

.role-default {
  background-color: #f8f9fa;
  color: #333;
}

/* Modals */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-container {
  background-color: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-container.large {
  max-width: 700px;
  max-height: 85vh;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.modal-header h3 {
  margin: 0;
  font-size: 1.5rem;
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
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

.modern-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.modern-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.checkbox-group {
  margin-bottom: 1.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-weight: 500;
  gap: 0.5rem;
}

.checkbox-label input[type="checkbox"] {
  margin-right: 0.5rem;
  transform: scale(1.2);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

/* Form Sections */
.form-section {
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.form-section:last-of-type {
  border-bottom: none;
  margin-bottom: 0;
}

.section-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.section-title::before {
  content: '';
  width: 4px;
  height: 1.2rem;
  background: var(--primary-color);
  border-radius: 2px;
}

/* Editor Assignment Controls */
.editor-assignment-controls {
  display: flex;
  gap: 1rem;
  align-items: end;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.select-group {
  flex: 1;
  min-width: 200px;
}

.select-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

/* Assignments List */
.assignments-list {
  background: var(--light-bg);
  border-radius: 8px;
  padding: 1rem;
  max-height: 300px;
  overflow-y: auto;
}

.empty-assignments {
  text-align: center;
  padding: 2rem;
  color: var(--text-secondary);
}

.empty-assignments p {
  margin: 0;
  font-style: italic;
}

.assignment-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: white;
  border-radius: 6px;
  margin-bottom: 0.5rem;
  box-shadow: var(--shadow-sm);
  transition: all 0.2s ease;
}

.assignment-item:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-1px);
}

.assignment-item:last-child {
  margin-bottom: 0;
}

.assignment-info {
  flex: 1;
}

.editor-name {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.assignment-meta {
  display: flex;
  gap: 1rem;
}

.assignment-date {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

/* Small action buttons */
.action-btn.small {
  padding: 0.4rem 0.8rem;
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

/* Icons */
.icon-user, .icon-plus, .icon-delete {
  font-size: 1rem;
}

/* Larger modal for year editing */
.modal-container.large {
  max-width: 700px;
  max-height: 85vh;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }

  .hero-stats {
    flex-direction: column;
    gap: 1.5rem;
  }

  .management-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .search-container {
    max-width: none;
  }

  .cards-grid {
    grid-template-columns: 1fr;
  }

  .modern-table {
    font-size: 0.9rem;
  }

  .modern-table td, .modern-table th {
    padding: 0.75rem 0.5rem;
  }

  .card-actions {
    flex-direction: column;
  }

  .action-btn {
    justify-content: center;
  }

  .editor-assignment-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .assignment-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .assignment-item .action-btn {
    align-self: flex-end;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 2rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .feature-card {
    padding: 1rem;
  }

  .modal-container {
    width: 95%;
    margin: 1rem;
  }
}
</style>