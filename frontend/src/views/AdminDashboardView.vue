<template>
  <div class="dashboard-container">
    <NavbarComponent />

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
            Komplexná správa systému konferenčných ročníkov a používateľov
          </p>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">{{ users.length }}</span>
              <span class="stat-label">Používatelia</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ articles.length }}</span>
              <span class="stat-label">Články</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ yearsWithEditors.length }}</span>
              <span class="stat-label">Ročníky</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ uploadedFiles.length }}</span>
              <span class="stat-label">Súbory</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa Ročníkov a Editorov</h2>
          <p class="section-subtitle">Spravujte konferenčné ročníky a priraďte k nim editorov</p>
        </div>

        <div class="management-actions">
          <button @click="openYearModal()" class="btn primary">
            <i class="icon">📅</i>
            Nový Ročník
          </button>
          <button @click="refreshAllData" class="btn secondary">
            <i class="icon">🔄</i>
            Obnoviť
          </button>
        </div>

        <div v-if="isLoadingYears" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Načítavam ročníky...</p>
        </div>

        <div v-else-if="yearsWithEditors.length === 0" class="empty-state">
          <div class="empty-icon">📅</div>
          <h3>Žiadne ročníky</h3>
          <p>Zatiaľ nie sú vytvorené žiadne konferenčné ročníky.</p>
          <button @click="openYearModal()" class="btn primary">
            Vytvoriť prvý ročník
          </button>
        </div>

        <div v-else class="years-grid">
          <div v-for="year in yearsWithEditors" :key="year.id" class="year-card">
            <div class="card-header">
              <div class="year-info">
                <h3>{{ formatConferenceYear(year) }}</h3>
                <div class="year-status">
                  <span :class="['status-badge', year.is_active ? 'active' : 'inactive']">
                    {{ year.is_active ? 'Aktívny' : 'Neaktívny' }}
                  </span>
                </div>
              </div>
              <div class="card-actions-header">
                <button @click="openYearModal(year)" class="action-btn-small edit" title="Upraviť ročník">
                  ✏️
                </button>
                <button @click="deleteYear(year.id)" class="action-btn-small delete" title="Vymazať ročník">
                  🗑️
                </button>
              </div>
            </div>

            <div class="card-content">
              <div class="editors-section">
                <div class="section-title-small">
                  <i class="icon">👥</i>
                  Priradení Editori ({{ year.editors.length }})
                </div>
                
                <div v-if="year.editors.length === 0" class="no-editors">
                  <div class="empty-icon-small">👤</div>
                  <p>Žiadni priradení editori</p>
                </div>

                <div v-else class="editors-list">
                  <div v-for="editor in year.editors" :key="editor.assignment_id" class="editor-item">
                    <div class="editor-avatar">
                      {{ editor.username.charAt(0).toUpperCase() }}
                    </div>
                    <div class="editor-info">
                      <strong>{{ editor.username }}</strong>
                      <span class="editor-email">{{ editor.email }}</span>
                      <span class="assigned-date">
                        📅 {{ formatDate(editor.assigned_at) }}
                      </span>
                    </div>
                    <button 
                      @click="removeEditorFromYear(year.id, editor.assignment_id)" 
                      class="remove-editor-btn"
                      title="Odstrániť editora">
                      ×
                    </button>
                  </div>
                </div>

                <div class="add-editor-section">
                  <div class="section-title-small">
                    <i class="icon">➕</i>
                    Pridať Editora
                  </div>
                  <div class="add-editor-form">
                    <select 
                      v-model="selectedEditorForYear[year.id]" 
                      class="editor-select"
                      :disabled="availableEditorsForYear(year).length === 0">
                      <option value="">
                        {{ availableEditorsForYear(year).length === 0 ? 'Žiadni dostupní editori' : 'Vyberte editora' }}
                      </option>
                      <option 
                        v-for="editor in availableEditorsForYear(year)" 
                        :key="editor.id" 
                        :value="editor.id">
                        {{ editor.username }} ({{ editor.email }})
                      </option>
                    </select>
                    <button 
                      @click="addEditorToYear(year.id)" 
                      class="btn primary small"
                      :disabled="!selectedEditorForYear[year.id]">
                      <i class="icon">👤</i>
                      Pridať
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="management-section alt-bg">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa Článkov</h2>
          <p class="section-subtitle">Vytvárajte a spravujte články pre jednotlivé ročníky</p>
        </div>

        <div class="management-actions">
          <div class="search-container">
            <div class="search-input-wrapper">
              <i class="search-icon">🔍</i>
              <input
                type="text"
                v-model="articleSearchQuery"
                placeholder="Hľadať články..."
                class="search-input"
              />
              <button v-if="articleSearchQuery" @click="articleSearchQuery = ''" class="clear-search">
                ×
              </button>
            </div>
          </div>
          <div class="filter-container">
            <select v-model="selectedConferenceYearFilter" class="filter-select">
              <option value="">Všetky ročníky</option>
              <option v-for="year in yearsWithEditors" :key="year.id" :value="year.id">
                {{ formatConferenceYear(year) }}
              </option>
            </select>
          </div>
          <button @click="openArticleModal()" class="btn primary">
            <i class="icon">📝</i>
            Nový Článok
          </button>
        </div>

        <div v-if="isLoadingArticles" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Načítavam články...</p>
        </div>

        <div v-else-if="filteredArticles.length === 0" class="empty-state">
          <div class="empty-icon">📝</div>
          <h3>{{ articleSearchQuery || selectedConferenceYearFilter ? 'Žiadne články nenájdené' : 'Žiadne články' }}</h3>
          <p>{{ articleSearchQuery || selectedConferenceYearFilter ? 'Skúste zmeniť filter alebo vyhľadávací dotaz.' : 'Zatiaľ nie sú vytvorené žiadne články.' }}</p>
          <button @click="openArticleModal()" class="btn primary">
            Vytvoriť prvý článok
          </button>
        </div>

        <div v-else class="articles-grid">
          <div v-for="article in filteredArticles" :key="article.id" class="article-card">
            <div class="card-header">
              <div class="article-info">
                <h3>{{ article.title }}</h3>
                <div class="article-meta">
                  <div class="meta-item">
                    <i class="icon">👤</i>
                    <span>{{ article.author_name }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="icon">📅</i>
                    <span>{{ formatConferenceYear(article.conference_year) }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="icon">🕒</i>
                    <span>{{ formatDate(article.created_at) }}</span>
                  </div>
                </div>
              </div>
              <div class="card-actions-header">
                <button @click="viewArticle(article)" class="action-btn-small view" title="Zobraziť článok">
                  👁️
                </button>
                <button @click="openArticleModal(article)" class="action-btn-small edit" title="Upraviť článok">
                  ✏️
                </button>
                <button @click="deleteArticle(article.id)" class="action-btn-small delete" title="Vymazať článok">
                  🗑️
                </button>
              </div>
            </div>

            <div class="card-content">
              <div class="article-preview">
                {{ getContentPreview(article.content) }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa Súborov</h2>
          <p class="section-subtitle">Nahrávajte a spravujte súbory pre TinyMCE editor</p>
        </div>

        <div class="management-actions">
          <div class="search-container">
            <div class="search-input-wrapper">
              <i class="search-icon">🔍</i>
              <input
                type="text"
                v-model="fileSearchQuery"
                placeholder="Hľadať súbory..."
                class="search-input"
              />
              <button v-if="fileSearchQuery" @click="fileSearchQuery = ''" class="clear-search">
                ×
              </button>
            </div>
          </div>
          <button @click="triggerFileUpload" class="btn primary">
            <i class="icon">📎</i>
            Nahrať Súbor
          </button>
          <button @click="refreshFiles" class="btn secondary">
            <i class="icon">🔄</i>
            Obnoviť
          </button>
        </div>

        <div 
          class="file-upload-area"
          :class="{ 'dragover': isDragOver }"
          @click="triggerFileUpload"
          @dragover.prevent="isDragOver = true"
          @dragleave.prevent="isDragOver = false"
          @drop.prevent="handleFileDrop">
          <div class="upload-icon">📁</div>
          <h3 class="upload-text">Kliknite alebo pretiahnite súbory sem</h3>
          <p class="upload-hint">Maximálna veľkosť súboru: 10MB</p>
        </div>

        <input 
          ref="fileInput" 
          type="file" 
          multiple 
          style="display: none" 
          @change="handleFileUpload" 
        />

        <div v-if="isLoadingFiles" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Načítavam súbory...</p>
        </div>

        <div v-else-if="filteredFiles.length === 0" class="empty-state">
          <div class="empty-icon">📁</div>
          <h3>{{ fileSearchQuery ? 'Žiadne súbory nenájdené' : 'Žiadne súbory' }}</h3>
          <p>{{ fileSearchQuery ? 'Skúste zmeniť vyhľadávací dotaz.' : 'Zatiaľ nie sú nahrané žiadne súbory.' }}</p>
          <button @click="triggerFileUpload" class="btn primary">
            Nahrať prvý súbor
          </button>
        </div>

        <div v-else class="files-grid">
          <div v-for="file in filteredFiles" :key="file.id" class="file-card">
            <div class="card-header">
              <div class="file-info">
                <h3>{{ file.original_name }}</h3>
                <div class="file-meta">
                  <div class="meta-item">
                    <i class="icon">{{ getFileIcon(file) }}</i>
                    <span>{{ file.mime_type }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="icon">📏</i>
                    <span>{{ file.file_size_human }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="icon">🕒</i>
                    <span>{{ formatDate(file.created_at) }}</span>
                  </div>
                </div>
              </div>
              <div class="card-actions-header">
                <button @click="downloadFile(file)" class="action-btn-small download" title="Stiahnuť súbor">
                  💾
                </button>
                <button @click="copyFileUrl(file)" class="action-btn-small copy" title="Kopírovať URL">
                  📋
                </button>
                <button @click="deleteFile(file.id)" class="action-btn-small delete" title="Vymazať súbor">
                  🗑️
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="management-section alt-bg">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa Používateľov</h2>
          <p class="section-subtitle">Vytvárajte a spravujte používateľské účty</p>
        </div>

        <div class="management-actions">
          <div class="search-container">
            <div class="search-input-wrapper">
              <i class="search-icon">🔍</i>
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Hľadať používateľov..."
                class="search-input"
              />
              <button v-if="searchQuery" @click="searchQuery = ''" class="clear-search">
                ×
              </button>
            </div>
          </div>
          <button @click="showCreateUserModal = true" class="btn primary">
            <i class="icon">👤</i>
            Nový Používateľ
          </button>
        </div>

        <div v-if="isLoadingUsers" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Načítavam používateľov...</p>
        </div>

        <div v-else-if="filteredUsers.length === 0" class="empty-state">
          <div class="empty-icon">👥</div>
          <h3>{{ searchQuery ? 'Žiadni používatelia nenájdení' : 'Žiadni používatelia' }}</h3>
          <p>{{ searchQuery ? 'Skúste zmeniť vyhľadávací dotaz.' : 'Zatiaľ nie sú vytvorení žiadni používatelia.' }}</p>
        </div>

        <div v-else class="users-table-container">
          <table class="users-table">
            <thead>
              <tr>
                <th>
                  <i class="icon">👤</i>
                  Používateľ
                </th>
                <th>
                  <i class="icon">📧</i>
                  Email
                </th>
                <th>
                  <i class="icon">🏷️</i>
                  Rola
                </th>
                <th>
                  <i class="icon">📅</i>
                  Vytvorený
                </th>
                <th>Akcie</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in filteredUsers" :key="user.id" class="user-row">
                <td>
                  <div class="user-cell">
                    <div class="user-avatar">
                      {{ user.username.charAt(0).toUpperCase() }}
                    </div>
                    <span class="user-name">{{ user.username }}</span>
                  </div>
                </td>
                <td>{{ user.email }}</td>
                <td>
                  <span :class="['role-badge', getRoleBadgeClass(user.roles?.[0]?.name || 'no-role')]">
                    {{ user.roles?.[0]?.name || 'Bez role' }}
                  </span>
                </td>
                <td>{{ formatDate(user.created_at) }}</td>
                <td>
                  <div class="action-buttons">
                    <button @click="openEditUserModal(user)" class="btn-edit" title="Upraviť">
                      ✏️
                    </button>
                    <button @click="openPasswordChangeModal(user)" class="btn-edit" title="Zmeniť heslo">
                      🔑
                    </button>
                    <button @click="openDeleteConfirmModal(user)" class="btn-delete" title="Vymazať">
                      🗑️
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <div v-if="showYearModal" class="modal-overlay" @click="closeYearModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>
            <i class="icon">📅</i>
            {{ editingYear ? 'Upraviť ročník' : 'Nový ročník' }}
          </h3>
          <button @click="closeYearModal" class="close-button">&times;</button>
        </div>

        <form @submit.prevent="saveYear" class="modal-form">
          <div class="form-group">
            <label for="yearValue">
              <i class="icon">📅</i>
              Rok
            </label>
            <input
              id="yearValue"
              type="text"
              v-model="yearForm.year"
              required
              class="modern-input"
              placeholder="napr. 2024"
            />
          </div>

          <div class="form-group">
            <label for="semester">
              <i class="icon">🗓️</i>
              Semester
            </label>
            <select
              id="semester"
              v-model="yearForm.semester"
              required
              class="modern-select"
            >
              <option value="">Vyberte semester</option>
              <option value="Winter">Winter</option>
              <option value="Summer">Summer</option>
            </select>
          </div>

          <div class="form-group checkbox-group">
            <label class="checkbox-label">
              <input
                type="checkbox"
                v-model="yearForm.is_active"
                class="checkbox-input"
              />
              <span class="checkbox-custom"></span>
              <span class="checkbox-text">
                <i class="icon">✅</i>
                Aktívny ročník
              </span>
            </label>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeYearModal" class="btn secondary">
              <i class="icon">❌</i>
              Zrušiť
            </button>
            <button type="submit" class="btn primary">
              <i class="icon">💾</i>
              {{ editingYear ? 'Aktualizovať' : 'Vytvoriť' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showArticleModal" class="modal-overlay" @click="closeArticleModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>
            <i class="icon">📝</i>
            {{ editingArticle ? 'Upraviť článok' : 'Nový článok' }}
          </h3>
          <button @click="closeArticleModal" class="close-button">&times;</button>
        </div>

        <form @submit.prevent="saveArticle" class="modal-form">
          <div class="form-group">
            <label for="articleTitle">
              <i class="icon">📝</i>
              Názov článku
            </label>
            <input
              id="articleTitle"
              type="text"
              v-model="articleForm.title"
              required
              class="modern-input"
              placeholder="Zadajte názov článku"
            />
          </div>

          <div class="form-group">
            <label for="articleAuthor">
              <i class="icon">👤</i>
              Autor
            </label>
            <input
              id="articleAuthor"
              type="text"
              v-model="articleForm.author_name"
              required
              class="modern-input"
              placeholder="Meno autora"
            />
          </div>

          <div class="form-group">
            <label for="articleConferenceYear">
              <i class="icon">📅</i>
              Ročník konferencie
            </label>
            <select
              id="articleConferenceYear"
              v-model="articleForm.conference_year_id"
              required
              class="modern-select"
            >
              <option value="">Vyberte ročník</option>
              <option v-for="year in yearsWithEditors" :key="year.id" :value="year.id">
                {{ formatConferenceYear(year) }}
              </option>
            </select>
          </div>

          <div class="form-group editor-group">
            <label for="articleContent">
              <i class="icon">📄</i>
              Obsah článku
            </label>
            <div class="editor-wrapper">
              <Editor
                :api-key="tinyMceApiKey"
                v-model="articleForm.content"
                :init="editorConfig"
                class="full-editor"
              />
            </div>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeArticleModal" class="btn secondary">
              <i class="icon">❌</i>
              Zrušiť
            </button>
            <button type="submit" class="btn primary">
              <i class="icon">💾</i>
              {{ editingArticle ? 'Aktualizovať' : 'Vytvoriť' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showArticleViewModal" class="modal-overlay" @click="closeArticleViewModal">
      <div class="modal-container large" @click.stop>
        <div class="modal-header">
          <h3>
            <i class="icon">👁️</i>
            {{ viewingArticle?.title }}
          </h3>
          <button @click="closeArticleViewModal" class="close-button">&times;</button>
        </div>

        <div class="modal-body">
          <div class="article-details">
            <div class="article-meta-view">
              <div class="meta-item">
                <strong>Autor:</strong> {{ viewingArticle?.author_name }}
              </div>
              <div class="meta-item">
                <strong>Ročník:</strong> {{ formatConferenceYear(viewingArticle?.conference_year) }}
              </div>
              <div class="meta-item">
                <strong>Vytvorené:</strong> {{ formatDate(viewingArticle?.created_at) }}
              </div>
            </div>
            <div class="article-content-view" v-html="viewingArticle?.content"></div>
          </div>
        </div>
      </div>
    </div>

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

    <!-- Password Change Modal -->
    <PasswordChangeModal 
      v-if="showPasswordChangeModal" 
      @close="closePasswordChangeModal" 
      @password-updated="handlePasswordUpdated"
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
import DeleteConfirmationModal from '@/components/modals/DeleteConfirmationModal.vue'
import PasswordChangeModal from '@/components/modals/PasswordChangeModal.vue'


const yearsWithEditors = ref<any[]>([])
const editors = ref<any[]>([])
const users = ref<any[]>([])
const roles = ref<any[]>([])
const articles = ref<any[]>([])
const uploadedFiles = ref<any[]>([])
const isLoadingYears = ref(false)
const isLoadingUsers = ref(false)
const isLoadingArticles = ref(false)
const isLoadingFiles = ref(false)
const selectedEditorForYear = ref<Record<number, number>>({})
const searchQuery = ref('')
const articleSearchQuery = ref('')
const fileSearchQuery = ref('')
const selectedConferenceYearFilter = ref('')
const isDragOver = ref(false)


const showYearModal = ref(false)
const editingYear = ref<any>(null)
const yearForm = ref({ semester: '', year: '', is_active: false })

// Article form
const showArticleModal = ref(false)
const showArticleViewModal = ref(false)
const editingArticle = ref<any>(null)
const viewingArticle = ref<any>(null)
const articleForm = ref({
  title: '',
  content: '',
  author_name: '',
  conference_year_id: ''
})

// Modal states
const showCreateUserModal = ref(false)
const showEditUserModal = ref(false)
const showDeleteConfirmModal = ref(false)
const showPasswordChangeModal = ref(false)
const selectedUser = ref<any>(null)

// File upload
const fileInput = ref<HTMLInputElement>()

// TinyMCE configuration - KOMPLETNE NOVÁ KONFIGURÁCIA
const tinyMceApiKey = 'ama3uyd2ecm9bw4zvg1689uk4qkpcxzv7sxvjv47ylo35cen' 
const editorConfig = ref({
  height: 700,
  menubar: 'file edit view insert format tools table help',
  plugins: [
    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
    'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
    'template', 'codesample', 'hr', 'pagebreak', 'nonbreaking', 'toc',
    'imagetools', 'textpattern', 'noneditable', 'quickbars', 'accordion',
    'autosave', 'directionality', 'visualchars', 'importcss'
  ],
  
  // Toolbar configuration
  toolbar: [
    'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks',
    'alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist checklist',
    'forecolor backcolor removeformat | pagebreak | charmap emoticons',
    'link anchor | image media table | lineheight | hr | codesample',
    'fullscreen preview save print | ltr rtl | template | accordion | code visualblocks'
  ].join(' | '),
  
  // Quickbars
  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
  quickbars_insert_toolbar: 'quickimage quicktable hr pagebreak',
  contextmenu: 'link image table configurepermanentpen',
  
  // Image handling
  automatic_uploads: true,
  file_picker_types: 'image media file',
  
  // File picker callback
  file_picker_callback: function (callback: any, value: any, meta: any) {
    const input = document.createElement('input')
    input.setAttribute('type', 'file')
    
    if (meta.filetype === 'image') {
      input.setAttribute('accept', 'image/*')
    } else if (meta.filetype === 'media') {
      input.setAttribute('accept', 'video/*,audio/*')
    } else {
      input.setAttribute('accept', '*/*')
    }
    
    input.addEventListener('change', async function(e: any) {
      const file = e.target.files[0]
      if (file) {
        try {
          const formData = new FormData()
          formData.append('file', file)
          
          const response = await adminPanel.uploadFile(formData)
          
          callback(response.data.location || response.data.url, {
            text: file.name,
            title: file.name,
            alt: file.name
          })
        } catch (error) {
          console.error('File upload failed:', error)
          alert('Chyba pri nahrávaní súboru')
        }
      }
    })
    
    input.click()
  },
  
  // Images upload handler
  images_upload_handler: async (blobInfo: any, progress: any) => {
    return new Promise(async (resolve, reject) => {
      try {
        const formData = new FormData()
        formData.append('file', blobInfo.blob(), blobInfo.filename())
        
        const response = await adminPanel.uploadFile(formData)
        resolve(response.data.location || response.data.url)
      } catch (error) {
        console.error('Image upload failed:', error)
        reject('Image upload failed: ' + error)
      }
    })
  },
  
  // Images upload URL
  images_upload_url: '/api/upload-file',
  images_upload_credentials: true,
  
  // Content styling
  content_style: `
    body { 
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
      font-size: 16px; 
      line-height: 1.6; 
      color: #111827;
      max-width: 100%;
      margin: 0 auto;
      padding: 20px;
      background: white;
    }
    
    h1, h2, h3, h4, h5, h6 { 
      font-weight: 700; 
      margin-top: 2rem; 
      margin-bottom: 1rem; 
      line-height: 1.25;
      color: #1f2937;
    }
    h1 { font-size: 2.5rem; color: #3b82f6; }
    h2 { font-size: 2rem; color: #1e40af; }
    h3 { font-size: 1.75rem; color: #1d4ed8; }
    h4 { font-size: 1.5rem; color: #2563eb; }
    h5 { font-size: 1.25rem; color: #3b82f6; }
    h6 { font-size: 1.125rem; color: #60a5fa; }
    
    p { 
      margin-bottom: 1rem; 
      text-align: justify;
    }
    
    blockquote { 
      border-left: 4px solid #3b82f6; 
      padding: 1rem 1.5rem; 
      margin: 1.5rem 0; 
      font-style: italic; 
      color: #6b7280; 
      background: #f8fafc;
      border-radius: 0 8px 8px 0;
    }
    
    table { 
      border-collapse: collapse; 
      width: 100%; 
      margin: 1.5rem 0;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    table th, table td { 
      border: 1px solid #e5e7eb; 
      padding: 1rem; 
      text-align: left; 
      vertical-align: top;
    }
    
    table th { 
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); 
      font-weight: 700; 
      color: #1f2937;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    table tr:nth-child(even) {
      background: #f9fafb;
    }
    
    table tr:hover {
      background: #f3f4f6;
    }
    
    img { 
      max-width: 100%; 
      height: auto; 
      border-radius: 8px; 
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      margin: 1rem 0;
    }
    
    code { 
      background: #f1f5f9; 
      padding: 0.25rem 0.5rem; 
      border-radius: 4px; 
      font-family: 'JetBrains Mono', 'Courier New', monospace; 
      font-size: 0.875rem;
      color: #dc2626;
      border: 1px solid #e2e8f0;
    }
    
    pre { 
      background: #0f172a; 
      color: #e2e8f0;
      padding: 1.5rem; 
      border-radius: 8px; 
      overflow-x: auto; 
      margin: 1.5rem 0;
      border: 1px solid #374151;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    pre code {
      background: none;
      border: none;
      color: inherit;
      padding: 0;
    }
    
    .mce-content-body hr {
      border: none;
      height: 3px;
      background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
      margin: 2rem 0;
      border-radius: 2px;
    }
    
    ul, ol {
      padding-left: 2rem;
      margin: 1rem 0;
    }
    
    li {
      margin-bottom: 0.5rem;
      line-height: 1.6;
    }
    
    a {
      color: #3b82f6;
      text-decoration: none;
      border-bottom: 1px solid transparent;
      transition: all 0.2s ease;
    }
    
    a:hover {
      color: #1e40af;
      border-bottom-color: #3b82f6;
    }
    
    .toc {
      background: #f8fafc;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      padding: 1.5rem;
      margin: 2rem 0;
    }
    
    .toc h2 {
      margin-top: 0;
      color: #1f2937;
      font-size: 1.25rem;
    }
    
    .accordion {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      margin: 1rem 0;
      overflow: hidden;
    }
    
    .accordion-header {
      background: #f8fafc;
      padding: 1rem;
      cursor: pointer;
      border-bottom: 1px solid #e5e7eb;
      font-weight: 600;
    }
    
    .accordion-content {
      padding: 1rem;
      background: white;
    }
    
    mark {
      background: #fef3c7;
      color: #92400e;
      padding: 0.125rem 0.25rem;
      border-radius: 3px;
    }
    
    .pagebreak {
      page-break-before: always;
      border-top: 2px dashed #9ca3af;
      margin: 2rem 0;
      padding-top: 1rem;
      text-align: center;
      color: #6b7280;
      font-size: 0.875rem;
    }
    
    .pagebreak::before {
      content: "Page Break";
    }
  `,
  
  // Font options
  font_family_formats: 
    'Inter=Inter,sans-serif;' +
    'Arial=arial,helvetica,sans-serif;' +
    'Times New Roman=times new roman,times;' +
    'Georgia=georgia,palatino;' +
    'Verdana=verdana,geneva;' +
    'Courier New=courier new,courier,monospace;' +
    'JetBrains Mono=JetBrains Mono,monospace;' +
    'Comic Sans MS=comic sans ms,sans-serif',
    
  font_size_formats: '8px 9px 10px 11px 12px 14px 16px 18px 20px 22px 24px 26px 28px 32px 36px 48px 60px 72px 96px',
  
  // Line height options
  lineheight_formats: '1 1.1 1.2 1.3 1.4 1.5 1.6 1.8 2 2.5 3',
  
  // Block formats
  block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre; Blockquote=blockquote; Div=div',
  
  // Templates
  templates: [
    {
      title: 'Základná štruktúra článku',
      description: 'Štandardná štruktúra pre konferenčný článok',
      content: `
        <h1>Názov článku</h1>
        <p><strong>Autor:</strong> Meno autora</p>
        <p><strong>Kľúčové slová:</strong> kľúčové, slová, článku</p>
        
        <h2>Abstrakt</h2>
        <blockquote>
          <p>Krátky popis článku a jeho hlavných prínosov. Abstrakt by mal obsahovať ciele, metodológiu, hlavné výsledky a závery práce.</p>
        </blockquote>
        
        <h2>1. Úvod</h2>
        <p>Úvodný text, ktorý predstavuje problematiku a ciele práce...</p>
        
        <h2>2. Súvisiace práce</h2>
        <p>Prehľad existujúcich riešení a prístupov...</p>
        
        <h2>3. Metodológia</h2>
        <p>Popis použitej metodológie a prístupu...</p>
        
        <h2>4. Implementácia</h2>
        <p>Detaily implementácie riešenia...</p>
        
        <h2>5. Výsledky</h2>
        <p>Prezentácia dosiahnutých výsledkov...</p>
        
        <h2>6. Diskusia</h2>
        <p>Diskusia o výsledkoch a ich interpretácia...</p>
        
        <h2>7. Záver</h2>
        <p>Záverečné zhrnutie a možnosti ďalšieho výskumu...</p>
        
        <h2>Referencie</h2>
        <ol>
          <li>Autor, A. (2024). Názov publikácie. Vydavateľstvo.</li>
          <li>Autor, B. (2024). Názov článku. Časopis, 1(1), 1-10.</li>
        </ol>
      `
    },
    {
      title: 'Tabuľka porovnania',
      description: 'Tabuľka pre porovnanie metód alebo technológií',
      content: `
        <h3>Porovnanie metód</h3>
        <table style="width: 100%;">
          <thead>
            <tr>
              <th>Metóda</th>
              <th>Výhody</th>
              <th>Nevýhody</th>
              <th>Použitie</th>
              <th>Hodnotenie</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Metóda A</td>
              <td>Rýchla, jednoduchá</td>
              <td>Menej presná</td>
              <td>Základné aplikácie</td>
              <td>⭐⭐⭐</td>
            </tr>
            <tr>
              <td>Metóda B</td>
              <td>Veľmi presná</td>
              <td>Pomalšia</td>
              <td>Pokročilé aplikácie</td>
              <td>⭐⭐⭐⭐⭐</td>
            </tr>
          </tbody>
        </table>
      `
    },
    {
      title: 'Kód s vysvetlením',
      description: 'Blok kódu s komentármi',
      content: `
        <h3>Implementácia algoritmu</h3>
        <p>Nasledujúci kód implementuje základný algoritmus:</p>
        <pre><code class="language-javascript">
function exampleAlgorithm(data) {
    // Inicializácia premenných
    let result = [];
    
    // Iterácia cez dáta
    for (let i = 0; i < data.length; i++) {
        // Spracovanie každého prvku
        if (data[i] > 0) {
            result.push(data[i] * 2);
        }
    }
    
    return result;
}
        </code></pre>
        <p>Tento algoritmus spracováva vstupné dáta a vracia výsledok...</p>
      `
    },
    {
      title: 'Obrázok s popisom',
      description: 'Štruktúra pre obrázok s detailným popisom',
      content: `
        <h3>Diagram architektúry</h3>
        <p style="text-align: center;">
          <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y4ZmFmYyIgc3Ryb2tlPSIjZTVlN2ViIiBzdHJva2Utd2lkdGg9IjIiLz4KICA8dGV4dCB4PSIyMDAiIHk9IjEwMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzZiNzI4MCIgZm9udC1mYW1pbHk9IkludGVyLCBzYW5zLXNlcmlmIiBmb250LXNizemU9IjE0cHgiPk9icsOhem9rPC90ZXh0Pgo8L3N2Zz4K" alt="Príklad diagramu" style="max-width: 100%; height: auto;" />
        </p>
        <p><strong>Obrázok 1:</strong> Diagram znázorňuje architektúru systému s hlavnými komponentmi a ich vzájomnými súvislosťami.</p>
        <p>Ako je vidieť na obrázku, systém sa skladá z...</p>
      `
    }
  ],
  
  // Table options
  table_use_colgroups: true,
  table_default_attributes: {
    'border': '0',
    'cellpadding': '0',
    'cellspacing': '0'
  },
  table_default_styles: {
    'border-collapse': 'collapse',
    'width': '100%',
    'border': '2px solid #e5e7eb',
    'border-radius': '8px'
  },
  table_class_list: [
    { title: 'Štandardná tabuľka', value: 'table-standard' },
    { title: 'Tabuľka s pruhmi', value: 'table-striped' },
    { title: 'Kompaktná tabuľka', value: 'table-compact' },
    { title: 'Tabuľka s okrajmi', value: 'table-bordered' }
  ],
  
  // Advanced options
  paste_data_images: true,
  paste_as_text: false,
  paste_webkit_styles: 'all',
  paste_remove_styles_if_webkit: false,
  paste_merge_formats: true,
  
  // Custom formats
  formats: {
    alignleft: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', styles: { textAlign: 'left' } },
    aligncenter: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', styles: { textAlign: 'center' } },
    alignright: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', styles: { textAlign: 'right' } },
    alignjustify: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', styles: { textAlign: 'justify' } },
    bold: { inline: 'strong' },
    italic: { inline: 'em' },
    underline: { inline: 'u' },
    strikethrough: { inline: 'del' },
    superscript: { inline: 'sup' },
    subscript: { inline: 'sub' },
    code: { inline: 'code' },
    highlight: { inline: 'mark', styles: { backgroundColor: '#fef3c7', color: '#92400e' } },
    removeformat: [
      { selector: 'b,strong,em,i,font,u,strike,sub,sup,dfn,code,samp,kbd,var,cite,mark,q,del,ins', remove: 'all', split: true, expand: false, block_expand: true, deep: true },
      { selector: 'span', attributes: ['style', 'class'], remove: 'empty', split: true, expand: false, deep: true },
      { selector: '*', attributes: ['style', 'class'], split: false, expand: false, deep: true }
    ]
  },
  
  // Code sample options
  codesample_languages: [
    { text: 'HTML/XML', value: 'markup' },
    { text: 'JavaScript', value: 'javascript' },
    { text: 'TypeScript', value: 'typescript' },
    { text: 'CSS', value: 'css' },
    { text: 'PHP', value: 'php' },
    { text: 'Python', value: 'python' },
    { text: 'Java', value: 'java' },
    { text: 'C#', value: 'csharp' },
    { text: 'C++', value: 'cpp' },
    { text: 'SQL', value: 'sql' },
    { text: 'JSON', value: 'json' },
    { text: 'Bash', value: 'bash' }
  ],
  
  // Media options
  media_live_embeds: true,
  media_filter_html: false,
  
  // Link options
  link_assume_external_targets: true,
  link_context_toolbar: true,
  
  // Image options
  image_advtab: true,
  image_caption: true,
  image_list: [],
  
  // Autosave options
  autosave_ask_before_unload: true,
  autosave_interval: '30s',
  autosave_prefix: 'tinymce-autosave-{path}{query}-{id}-',
  autosave_restore_when_empty: false,
  autosave_retention: '2m',
  
  // Additional options
  browser_spellcheck: true,
  contextmenu_never_use_native: true,
  fix_list_elements: true,
  resize: true,
  branding: false,
  promotion: false,
  
  // Custom CSS classes
  content_css: [
    '//fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap'
  ],
  
  // Setup function
  setup: (editor: any) => {
    // Custom button for inserting university info
    editor.ui.registry.addButton('university_info', {
      text: '🏛️ Univerzita',
      tooltip: 'Vložiť informácie o univerzite',
      onAction: () => {
        editor.insertContent(`
          <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; margin: 1rem 0;">
            <h4 style="margin-top: 0; color: #1f2937;">Technická univerzita v Košiciach</h4>
            <p style="margin-bottom: 0;"><strong>Fakulta elektrotechniky a informatiky</strong><br>
            Letná 9, 042 00 Košice<br>
            <a href="https://www.tuke.sk">www.tuke.sk</a></p>
          </div>
        `)
      }
    })
    
    // Custom button for inserting citation
    editor.ui.registry.addButton('citation', {
      text: '📚 Citácia',
      tooltip: 'Vložiť citáciu',
      onAction: () => {
        editor.windowManager.open({
          title: 'Vložiť citáciu',
          body: {
            type: 'panel',
            items: [
              {
                type: 'input',
                name: 'author',
                label: 'Autor'
              },
              {
                type: 'input',
                name: 'title',
                label: 'Názov'
              },
              {
                type: 'input',
                name: 'year',
                label: 'Rok'
              },
              {
                type: 'input',
                name: 'publisher',
                label: 'Vydavateľ'
              }
            ]
          },
          buttons: [
            {
              type: 'cancel',
              text: 'Zrušiť'
            },
            {
              type: 'submit',
              text: 'Vložiť',
              primary: true
            }
          ],
          onSubmit: (api: any) => {
            const data = api.getData()
            const citation = `${data.author} (${data.year}). <em>${data.title}</em>. ${data.publisher}.`
            editor.insertContent(`<p style="margin-left: 2rem; font-style: italic;">${citation}</p>`)
            api.close()
          }
        })
      }
    })
    
    // Add custom toolbar
    editor.ui.registry.addGroupToolbarButton('customtools', {
      icon: 'more-drawer',
      tooltip: 'Ďalšie nástroje',
      items: 'university_info citation'
    })
  },
  
  // Extend toolbar with custom tools
  toolbar: [
    'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks',
    'alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist checklist',
    'forecolor backcolor removeformat | pagebreak | charmap emoticons',
    'link anchor | image media table | lineheight | hr | codesample',
    'fullscreen preview save print | ltr rtl | template | accordion | code visualblocks',
    'customtools'
  ].join(' | ')
})

// Computed
const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user => 
    user.username.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  )
})

const filteredArticles = computed(() => {
  let filtered = articles.value

  if (articleSearchQuery.value) {
    const query = articleSearchQuery.value.toLowerCase()
    filtered = filtered.filter(article =>
      article.title.toLowerCase().includes(query) ||
      article.author_name.toLowerCase().includes(query) ||
      article.content?.toLowerCase().includes(query)
    )
  }

  if (selectedConferenceYearFilter.value) {
    filtered = filtered.filter(article =>
      article.conference_year_id == selectedConferenceYearFilter.value
    )
  }

  return filtered
})

const filteredFiles = computed(() => {
  if (!fileSearchQuery.value) return uploadedFiles.value
  const query = fileSearchQuery.value.toLowerCase()
  return uploadedFiles.value.filter(file =>
    file.original_name.toLowerCase().includes(query) ||
    file.mime_type.toLowerCase().includes(query)
  )
})

// Get available editors for a specific year (editors not yet assigned to that year)
const availableEditorsForYear = (year: any) => {
  const assignedEditorIds = year.editors.map((editor: any) => editor.user_id)
  return editors.value.filter(editor => !assignedEditorIds.includes(editor.id))
}

// Methods
const formatConferenceYear = (year: any): string => {
  if (!year) return 'Neznámy ročník'
  return `${year.semester} ${year.year}`
}

const formatDate = (dateString?: string): string => {
  if (!dateString) return 'Neznámy dátum'
  return new Date(dateString).toLocaleDateString('sk-SK', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getRoleBadgeClass = (roleName: string): string => {
  switch (roleName) {
    case 'admin': return 'admin'
    case 'editor': return 'editor'
    default: return 'no-role'
  }
}

const getContentPreview = (content: string): string => {
  if (!content) return 'Žiadny obsah...'
  const plainText = content.replace(/<[^>]*>/g, '')
  return plainText.length > 150 ? plainText.substring(0, 150) + '...' : plainText
}

const getFileIcon = (file: any): string => {
  const mimeType = file.mime_type.toLowerCase()
  if (mimeType.includes('image')) return '🖼️'
  if (mimeType.includes('pdf')) return '📄'
  if (mimeType.includes('word') || mimeType.includes('document')) return '📝'
  if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return '📊'
  if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return '📊'
  if (mimeType.includes('video')) return '🎥'
  if (mimeType.includes('audio')) return '🎵'
  if (mimeType.includes('zip') || mimeType.includes('archive')) return '📦'
  return '📎'
}

// Load years with their editors
async function refreshYearsWithEditors() {
  try {
    isLoadingYears.value = true
    const response = await adminPanel.getYearsWithEditors()
    yearsWithEditors.value = response.data.data || []
  } catch (error) {
    console.error('Error loading years with editors:', error)
    alert('Chyba pri načítavaní ročníkov s editormi')
  } finally {
    isLoadingYears.value = false
  }
}

// Load available editors
async function refreshEditors() {
  try {
    const response = await adminPanel.getEditors()
    editors.value = response.data.editors || []
  } catch (error) {
    console.error('Error loading editors:', error)
  }
}

// Load users
async function refreshUsers() {
  try {
    isLoadingUsers.value = true
    const response = await adminPanel.getUsers()
    users.value = response.data.users || []
  } catch (error) {
    console.error('Error loading users:', error)
  } finally {
    isLoadingUsers.value = false
  }
}

// Load articles
async function refreshArticles() {
  try {
    isLoadingArticles.value = true
    const response = await adminPanel.listArticles()
    articles.value = response.data.data || []
  } catch (error) {
    console.error('Error loading articles:', error)
  } finally {
    isLoadingArticles.value = false
  }
}

// Load files - OPRAVENÁ FUNKCIA
async function refreshFiles() {
  try {
    isLoadingFiles.value = true
    console.log('Loading files...')
    
    // Skúsime najprv admin endpoint
    try {
      const response = await adminPanel.getAllFiles()
      uploadedFiles.value = response.data.data || []
      console.log('Files loaded successfully:', uploadedFiles.value.length)
    } catch (adminError) {
      console.warn('Admin files endpoint failed, trying user files:', adminError)
      // Ak admin endpoint nefunguje, skúsime user files
      const response = await adminPanel.getMyFiles()
      uploadedFiles.value = response.data.data || []
      console.log('User files loaded:', uploadedFiles.value.length)
    }
  } catch (error) {
    console.error('Error loading files:', error)
    uploadedFiles.value = []
    // Nie je to kritická chyba, pokračujeme bez súborov
  } finally {
    isLoadingFiles.value = false
  }
}

// Load roles
async function fetchRoles() {
  try {
    const response = await adminPanel.getRoles()
    roles.value = response.data.roles || []
  } catch (error) {
    console.error('Error loading roles:', error)
  }
}

// Refresh all data
async function refreshAllData() {
  await Promise.all([
    refreshYearsWithEditors(),
    refreshEditors(),
    refreshUsers(),
    refreshArticles(),
    refreshFiles(),
    fetchRoles()
  ])
}

// Add editor to year
async function addEditorToYear(yearId: number) {
  try {
    const editorId = selectedEditorForYear.value[yearId]
    if (!editorId) return

    await adminPanel.assignEditor(yearId, editorId)
    
    // Clear selection
    selectedEditorForYear.value[yearId] = 0
    
    // Refresh data
    await refreshYearsWithEditors()
    
    console.log('Editor successfully assigned to year')
  } catch (error) {
    console.error('Error assigning editor to year:', error)
    alert('Chyba pri priraďovaní editora k ročníku')
  }
}

// Remove editor from year
async function removeEditorFromYear(yearId: number, assignmentId: number) {
  try {
    if (!confirm('Naozaj chcete odstrániť tohto editora z ročníka?')) return

    await adminPanel.removeEditor(yearId, assignmentId)
    await refreshYearsWithEditors()
    
    console.log('Editor successfully removed from year')
  } catch (error) {
    console.error('Error removing editor from year:', error)
    alert('Chyba pri odstraňovaní editora z ročníka')
  }
}

// Year management
function openYearModal(year = null) {
  editingYear.value = year
  if (year) {
    yearForm.value = {
      semester: year.semester,
      year: year.year,
      is_active: year.is_active
    }
  } else {
    yearForm.value = { semester: '', year: '', is_active: false }
  }
  showYearModal.value = true
}

function closeYearModal() {
  showYearModal.value = false
  editingYear.value = null
  yearForm.value = { semester: '', year: '', is_active: false }
}

async function saveYear() {
  try {
    if (editingYear.value) {
      await adminPanel.updateYear(editingYear.value.id, yearForm.value)
    } else {
      await adminPanel.createYear(yearForm.value)
    }
    
    closeYearModal()
    await refreshYearsWithEditors()
  } catch (error) {
    console.error('Error saving year:', error)
    alert('Chyba pri ukladaní ročníka')
  }
}

async function deleteYear(id: number) {
  try {
    if (!confirm('Naozaj chcete vymazať tento ročník? Všetky súvisiace články a priraďovania budú odstránené.')) return

    await adminPanel.deleteYear(id)
    await refreshYearsWithEditors()
  } catch (error) {
    console.error('Error deleting year:', error)
    alert('Chyba pri mazaní ročníka')
  }
}

// Article management
function openArticleModal(article = null) {
  editingArticle.value = article
  if (article) {
    articleForm.value = {
      title: article.title,
      content: article.content || '',
      author_name: article.author_name,
      conference_year_id: article.conference_year_id
    }
  } else {
    articleForm.value = {
      title: '',
      content: '',
      author_name: '',
      conference_year_id: ''
    }
  }
  showArticleModal.value = true
}

function closeArticleModal() {
  showArticleModal.value = false
  editingArticle.value = null
  articleForm.value = {
    title: '',
    content: '',
    author_name: '',
    conference_year_id: ''
  }
}

async function saveArticle() {
  try {
    if (editingArticle.value) {
      await adminPanel.updateArticle(editingArticle.value.id, articleForm.value)
    } else {
      await adminPanel.createArticle(articleForm.value)
    }
    
    closeArticleModal()
    await refreshArticles()
  } catch (error) {
    console.error('Error saving article:', error)
    alert('Chyba pri ukladaní článku')
  }
}

async function deleteArticle(id: number) {
  try {
    if (!confirm('Naozaj chcete vymazať tento článok?')) return

    await adminPanel.deleteArticle(id)
    await refreshArticles()
  } catch (error) {
    console.error('Error deleting article:', error)
    alert('Chyba pri mazaní článku')
  }
}

function viewArticle(article: any) {
  viewingArticle.value = article
  showArticleViewModal.value = true
}

function closeArticleViewModal() {
  showArticleViewModal.value = false
  viewingArticle.value = null
}

// File management
function triggerFileUpload() {
  fileInput.value?.click()
}

async function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement
  const files = target.files
  if (files && files.length > 0) {
    await uploadFiles(Array.from(files))
  }
}

async function handleFileDrop(event: DragEvent) {
  isDragOver.value = false
  const files = event.dataTransfer?.files
  if (files && files.length > 0) {
    await uploadFiles(Array.from(files))
  }
}

async function uploadFiles(files: File[]) {
  try {
    const uploadPromises = files.map(async (file) => {
      const formData = new FormData()
      formData.append('file', file)
      
      try {
        const response = await adminPanel.uploadFile(formData)
        console.log('File uploaded successfully:', response.data)
        return response.data
      } catch (error) {
        console.error('Failed to upload file:', file.name, error)
        return null
      }
    })

    const results = await Promise.all(uploadPromises)
    const successCount = results.filter(result => result !== null).length
    
    if (successCount > 0) {
      alert(`Úspešne nahraných ${successCount} súborov`)
      await refreshFiles()
    } else {
      alert('Žiadne súbory neboli nahrané')
    }
  } catch (error) {
    console.error('Error during file upload:', error)
    alert('Chyba pri nahrávaní súborov')
  }
}

async function downloadFile(file: any) {
  try {
    window.open(file.download_url, '_blank')
  } catch (error) {
    console.error('Error downloading file:', error)
    alert('Chyba pri sťahovaní súboru')
  }
}

async function copyFileUrl(file: any) {
  try {
    await navigator.clipboard.writeText(file.download_url)
    alert('URL súboru bola skopírovaná do schránky')
  } catch (error) {
    console.error('Error copying URL:', error)
    alert('Chyba pri kopírovaní URL')
  }
}

async function deleteFile(id: number) {
  try {
    if (!confirm('Naozaj chcete vymazať tento súbor?')) return

    await adminPanel.deleteFile(id)
    await refreshFiles()
    alert('Súbor bol úspešne vymazaný')
  } catch (error) {
    console.error('Error deleting file:', error)
    alert('Chyba pri mazaní súboru')
  }
}

// User management
function openEditUserModal(user: any) {
  selectedUser.value = user
  showEditUserModal.value = true
}

function openDeleteConfirmModal(user: any) {
  selectedUser.value = user
  showDeleteConfirmModal.value = true
}

// Password change modal
function openPasswordChangeModal(user: any) {
  selectedUser.value = user
  showPasswordChangeModal.value = true
}

function closePasswordChangeModal() {
  showPasswordChangeModal.value = false
  selectedUser.value = null
}

function handlePasswordUpdated() {
  showPasswordChangeModal.value = false
  selectedUser.value = null
  // Refresh users if needed
  refreshUsers()
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
  selectedUser.value = null
  refreshUsers()
}

// Drag and drop handlers
onMounted(() => {
  document.addEventListener('dragover', (e) => e.preventDefault())
  document.addEventListener('drop', (e) => e.preventDefault())
})

// Initialize
onMounted(async () => {
  await refreshAllData()
})

// Provide for child components
provide('selectedUser', () => selectedUser.value)
provide('roles', () => roles.value)
provide('adminPanel', adminPanel)
</script>

<style scoped>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

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

.icon {
  font-style: normal;
  margin-right: 0.5rem;
  display: inline-block;
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

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 1.25rem;
  color: var(--text-light);
  z-index: 1;
  pointer-events: none;
  font-size: 1.125rem;
}

.search-input {
  width: 100%;
  padding: 1rem 1.25rem 1rem 3.5rem;
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

.search-input::placeholder {
  color: var(--text-light);
  font-weight: 400;
}

.clear-search {
  position: absolute;
  right: 1rem;
  background: var(--text-light);
  color: var(--text-white);
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: var(--radius-full);
  transition: var(--transition-fast);
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.clear-search:hover {
  background: var(--danger-color);
  transform: scale(1.1);
}

.filter-container {
  min-width: 200px;
}

.filter-select {
  width: 100%;
  padding: 1rem 1.25rem;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-xl);
  font-size: 1rem;
  background: var(--bg-secondary);
  transition: var(--transition);
  font-weight: 500;
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary-color);
  background: var(--bg-primary);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
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

.btn .icon {
  margin-right: 0.75rem;
  font-size: 1.125rem;
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

.btn.primary:active {
  transform: translateY(-1px);
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

.btn.small {
  padding: 0.75rem 1.5rem;
  font-size: 0.8rem;
  min-height: 40px;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Grid Layouts */
.years-grid,
.articles-grid,
.files-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 2rem;
  margin-top: 3rem;
}

/* Cards */
.year-card,
.article-card,
.file-card {
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

.year-card::before,
.article-card::before,
.file-card::before {
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

.year-card:hover::before,
.article-card:hover::before,
.file-card:hover::before {
  transform: scaleX(1);
}

.year-card:hover,
.article-card:hover,
.file-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-2xl);
  border-color: var(--border-dark);
}

.card-header {
  padding: 2rem;
  border-bottom: 1px solid var(--border-light);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-terciary) 100%);
  gap: 1rem;
}

.year-info h3,
.article-info h3,
.file-info h3 {
  margin: 0 0 0.75rem 0;
  color: var(--text-primary);
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1.3;
}

.article-meta,
.file-meta {
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

.meta-item .icon {
  margin-right: 0.5rem;
  color: var(--primary-color);
}

.status-badge {
  padding: 0.5rem 1rem;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border: 2px solid transparent;
  display: inline-block;
}

.status-badge.active {
  background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
  color: var(--text-white);
  border-color: #059669;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.status-badge.inactive {
  background: linear-gradient(135deg, var(--secondary-color) 0%, #4b5563 100%);
  color: var(--text-white);
  border-color: #4b5563;
  box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.card-actions-header {
  display: flex;
  gap: 0.75rem;
  flex-shrink: 0;
}

.action-btn-small {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-full);
  border: 2px solid transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.action-btn-small::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.2);
  transform: scale(0);
  border-radius: inherit;
  transition: transform 0.2s ease;
}

.action-btn-small:hover::before {
  transform: scale(1);
}

.action-btn-small.edit {
  background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
  color: var(--text-white);
  border-color: #d97706;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.action-btn-small.edit:hover {
  transform: scale(1.15);
  box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
}

.action-btn-small.delete {
  background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
  color: var(--text-white);
  border-color: #dc2626;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.action-btn-small.delete:hover {
  transform: scale(1.15);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
}

.action-btn-small.view {
  background: linear-gradient(135deg, var(--info-color) 0%, #0891b2 100%);
  color: var(--text-white);
  border-color: #0891b2;
  box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
}

.action-btn-small.view:hover {
  transform: scale(1.15);
  box-shadow: 0 8px 20px rgba(6, 182, 212, 0.4);
}

.card-content {
  padding: 2rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* Editors Section */
.editors-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.section-title-small {
  display: flex;
  align-items: center;
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid var(--border-light);
}

.section-title-small .icon {
  color: var(--primary-color);
  font-size: 1.25rem;
}

.no-editors {
  text-align: center;
  padding: 3rem 2rem;
  color: var(--text-secondary);
  background: var(--bg-secondary);
  border-radius: var(--radius-xl);
  border: 2px dashed var(--border-color);
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.empty-icon-small {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.no-editors p {
  font-size: 1rem;
  font-weight: 500;
}

.editors-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex: 1;
}

.editor-item {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  background: var(--bg-secondary);
  border-radius: var(--radius-xl);
  border: 1px solid var(--border-light);
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.editor-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.05), transparent);
  transition: left 0.6s ease;
}

.editor-item:hover::before {
  left: 100%;
}

.editor-item:hover {
  background: var(--bg-tertiary);
  transform: translateX(8px);
  border-color: var(--primary-color);
  box-shadow: var(--shadow-md);
}

.editor-avatar {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-full);
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: var(--text-white);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.125rem;
  margin-right: 1.5rem;
  flex-shrink: 0;
  border: 3px solid rgba(255, 255, 255, 0.9);
  box-shadow: var(--shadow-md);
}

.editor-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
}

.editor-info strong {
  color: var(--text-primary);
  font-weight: 700;
  font-size: 1rem;
}

.editor-email {
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
}

.assigned-date {
  color: var(--text-light);
  font-size: 0.8rem;
  font-weight: 500;
}

.remove-editor-btn {
  background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
  color: var(--text-white);
  border: 2px solid #dc2626;
  border-radius: var(--radius-full);
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 1.25rem;
  line-height: 1;
  transition: var(--transition);
  flex-shrink: 0;
  box-shadow: var(--shadow-sm);
}

.remove-editor-btn:hover {
  transform: scale(1.15);
  box-shadow: var(--shadow-lg);
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

.add-editor-section {
  border-top: 2px solid var(--border-light);
  padding-top: 1.5rem;
  margin-top: auto;
}

.add-editor-form {
  display: flex;
  gap: 1rem;
  align-items: flex-end;
}

.editor-select {
  flex: 1;
  padding: 1rem 1.25rem;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-xl);
  font-size: 0.875rem;
  background: var(--bg-primary);
  transition: var(--transition);
  font-weight: 500;
  cursor: pointer;
}

.editor-select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

/* Article Preview */
.article-preview {
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

.article-preview::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 30px;
  background: linear-gradient(transparent, var(--bg-secondary));
}

.article-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid var(--border-light);
}

/* File Management */
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
}

.file-upload-area::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.file-upload-area:hover::before {
  opacity: 1;
}

.file-upload-area:hover {
  border-color: var(--primary-color);
  background: var(--bg-primary);
  transform: translateY(-2px);
}

.file-upload-area.dragover {
  border-color: var(--success-color);
  background: rgba(16, 185, 129, 0.05);
}

.upload-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
  opacity: 0.6;
  color: var(--primary-color);
}

.upload-text {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.upload-hint {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.file-item {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  background: var(--bg-secondary);
  border-radius: var(--radius-xl);
  border: 1px solid var(--border-light);
  transition: var(--transition);
  margin-bottom: 1rem;
}

.file-item:hover {
  background: var(--bg-terciary);
  border-color: var(--primary-color);
  transform: translateX(4px);
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
  margin-right: 1.5rem;
  flex-shrink: 0;
}

.file-details {
  flex: 1;
}

.file-name {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.file-size {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

/* Tables */
.users-table-container {
  background: var(--bg-primary);
  border-radius: var(--radius-2xl);
  overflow: hidden;
  box-shadow: var(--shadow-lg);
  margin-top: 3rem;
  border: 1px solid var(--border-light);
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table th,
.users-table td {
  padding: 1.5rem 2rem;
  text-align: left;
  border-bottom: 1px solid var(--border-light);
}

.users-table th {
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-terciary) 100%);
  font-weight: 700;
  color: var(--text-primary);
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid var(--border-color);
}

.users-table th .icon {
  color: var(--primary-color);
  margin-right: 0.75rem;
}

.user-row {
  transition: var(--transition-fast);
}

.user-row:hover {
  background: var(--bg-secondary);
}

.user-row:last-child td {
  border-bottom: none;
}

.user-cell {
  display: flex;
  align-items: center;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-full);
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: var(--text-white);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  margin-right: 1rem;
  font-size: 0.875rem;
  border: 2px solid rgba(255, 255, 255, 0.9);
  box-shadow: var(--shadow-sm);
}

.user-name {
  font-weight: 600;
  color: var(--text-primary);
  font-size: 1rem;
}

.role-badge {
  padding: 0.5rem 1rem;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border: 2px solid transparent;
  display: inline-block;
}

.role-badge.admin {
  background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
  color: var(--text-white);
  border-color: #dc2626;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.role-badge.editor {
  background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
  color: var(--text-white);
  border-color: #d97706;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.role-badge.no-role {
  background: linear-gradient(135deg, var(--secondary-color) 0%, #4b5563 100%);
  color: var(--text-white);
  border-color: #4b5563;
  box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.action-buttons {
  display: flex;
  gap: 0.75rem;
}

.btn-edit,
.btn-delete {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-full);
  border: 2px solid transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.btn-edit::before,
.btn-delete::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.2);
  transform: scale(0);
  border-radius: inherit;
  transition: transform 0.2s ease;
}

.btn-edit:hover::before,
.btn-delete:hover::before {
  transform: scale(1);
}

.btn-edit {
  background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
  color: var(--text-white);
  border-color: #d97706;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.btn-edit:hover {
  transform: scale(1.15);
  box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
}

.btn-delete {
  background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
  color: var(--text-white);
  border-color: #dc2626;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-delete:hover {
  transform: scale(1.15);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
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

/* Modal Styles - UPRAVENÉ */
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
  width: 95vw;
  max-width: 1200px;
  max-height: 95vh;
  height: 95vh;
  overflow-y: auto;
  box-shadow: var(--shadow-2xl);
  border: 1px solid var(--border-light);
  animation: slideUp 0.3s ease;
  position: relative;
  margin: 0;
}

/* Editor Modal - FULL SCREEN */
.modal-container.editor-modal {
  max-width: 95vw;
  max-height: 95vh;
  width: 95vw;
  height: 95vh;
  margin: 0;
}

.modal-container.large {
  max-width: 900px;
  max-height: 90vh;
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
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-terciary) 100%);
  flex-shrink: 0;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.5rem;
  color: var(--text-primary);
  font-weight: 700;
  display: flex;
  align-items: center;
}

.modal-header h3 .icon {
  color: var(--primary-color);
  font-size: 1.75rem;
  margin-right: 1rem;
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
  height: calc(100% - 140px); /* Adjust for header and footer */
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.modal-body {
  padding: 2.5rem;
  overflow-y: auto;
  flex: 1;
}

.form-group {
  margin-bottom: 2rem;
}

.form-group.editor-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 400px;
}

.form-group label {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
  font-weight: 700;
  color: var(--text-primary);
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-group label .icon {
  color: var(--primary-color);
  font-size: 1.125rem;
  margin-right: 0.75rem;
}

.modern-input,
.modern-select,
.modern-textarea {
  width: 100%;
  padding: 1.25rem 1.5rem;
  border: 1px solid black;
  border-radius: 25px;
  font-size: 1rem;
  background: var(--bg-secondary);
  transition: var(--transition);
  font-weight: 500;
  font-family: inherit;
}

.modern-input:focus,
.modern-select:focus,
.modern-textarea:focus {
  outline: none;
  border-color: var(--primary-color);
  background: var(--bg-primary);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  transform: translateY(-1px);
}

.modern-textarea {
  min-height: 120px;
  resize: vertical;
}

/* TinyMCE Editor Styles - ROZŠÍRENÉ */
.editor-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 600px;
}

.full-editor {
  height: 100% !important;
  min-height: 600px !important;
}

.editor-wrapper .tox-tinymce {
  border-radius: var(--radius-lg) !important;
  border: 2px solid var(--border-color) !important;
  box-shadow: var(--shadow-lg) !important;
  transition: var(--transition) !important;
  height: 100% !important;
  min-height: 600px !important;
}

.editor-wrapper .tox-tinymce:focus-within {
  border-color: var(--primary-color) !important;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
}

.editor-wrapper .tox-toolbar {
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-terciary) 100%) !important;
  border-bottom: 1px solid var(--border-color) !important;
  padding: 0.5rem !important;
}

.editor-wrapper .tox-toolbar-overlord {
  background: transparent !important;
}

.editor-wrapper .tox-toolbar__group {
  border: none !important;
}

.editor-wrapper .tox-tbtn {
  margin: 2px !important;
  border-radius: var(--radius-sm) !important;
  transition: var(--transition-fast) !important;
}

.editor-wrapper .tox-tbtn:hover {
  background: var(--primary-color) !important;
  color: var(--text-white) !important;
}

.editor-wrapper .tox-tbtn--enabled {
  background: var(--primary-color) !important;
  color: var(--text-white) !important;
}

.editor-wrapper .tox-edit-area {
  background: var(--bg-primary) !important;
}

.editor-wrapper .tox-edit-area__iframe {
  background: var(--bg-primary) !important;
  min-height: 500px !important;
}

.editor-wrapper .tox-statusbar {
  background: var(--bg-secondary) !important;
  border-top: 1px solid var(--border-color) !important;
  color: var(--text-secondary) !important;
}

.editor-wrapper .tox-dialog {
  border-radius: var(--radius-xl) !important;
  box-shadow: var(--shadow-2xl) !important;
}

.editor-wrapper .tox-dialog-header {
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-terciary) 100%) !important;
  border-bottom: 1px solid var(--border-color) !important;
}

.editor-wrapper .tox-button {
  border-radius: var(--radius-md) !important;
  transition: var(--transition-fast) !important;
}

.editor-wrapper .tox-button:hover {
  background: var(--primary-color) !important;
  color: var(--text-white) !important;
}

.editor-wrapper .tox-button--primary {
  background: var(--primary-color) !important;
}

.editor-wrapper .tox-button--secondary {
  background: var(--secondary-color) !important;
}

/* Modal editor responsive */
@media (max-width: 768px) {
  .editor-wrapper {
    min-height: 400px;
  }
  
  .full-editor {
    min-height: 400px !important;
  }
  
  .editor-wrapper .tox-edit-area__iframe {
    min-height: 350px !important;
  }
}

/* Full screen editor styles */
.tox-fullscreen {
  z-index: 10000 !important;
}

.tox-fullscreen .tox-edit-area__iframe {
  min-height: calc(100vh - 200px) !important;
}

/* Modal Form Actions - OPRAVENÉ TLAČIDLÁ */
.modal-form .form-actions,
.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 2.5rem;
  border-top: 1px solid var(--border-light);
  background: var(--bg-secondary);
  margin: 0;
  gap: 1rem;
  flex-wrap: wrap;
}

.form-actions .left-actions {
  display: flex;
  gap: 1rem;
}

.form-actions .right-actions {
  display: flex;
  gap: 1rem;
  margin-left: auto;
}

/* Modal Buttons - KOMPLETNE NOVÉ ŠTÝLY */
.modal-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.875rem 2rem;
  border-radius: var(--radius-lg);
  font-weight: 600;
  font-size: 0.875rem;
  text-decoration: none;
  cursor: pointer;
  border: none;
  transition: var(--transition);
  white-space: nowrap;
  min-height: 44px;
  min-width: 120px;
  position: relative;
  overflow: hidden;
}

.modal-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.modal-btn:hover::before {
  left: 100%;
}

.modal-btn .icon {
  margin-right: 0.75rem;
  font-size: 1rem;
}

/* Primary Button */
.modal-btn.primary {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: var(--text-white);
  box-shadow: var(--shadow-md);
}

.modal-btn.primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
}

/* Success Button */
.modal-btn.success {
  background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
  color: var(--text-white);
  box-shadow: var(--shadow-md);
}

.modal-btn.success:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  background: linear-gradient(135deg, #10b981 0%, var(--success-color) 100%);
}

/* Secondary Button */
.modal-btn.secondary {
  background: linear-gradient(135deg, var(--secondary-color) 0%, #4b5563 100%);
  color: var(--text-white);
  box-shadow: var(--shadow-md);
}

.modal-btn.secondary:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
}

/* Danger Button */
.modal-btn.danger {
  background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
  color: var(--text-white);
  box-shadow: var(--shadow-md);
}

.modal-btn.danger:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  background: linear-gradient(135deg, #f87171 0%, var(--danger-color) 100%);
}

/* Outline Button */
.modal-btn.outline {
  background: transparent;
  color: var(--text-secondary);
  border: 2px solid var(--border-color);
  box-shadow: none;
}

.modal-btn.outline:hover {
  background: var(--bg-secondary);
  border-color: var(--primary-color);
  color: var(--primary-color);
  transform: translateY(-1px);
}

/* Disabled Button */
.modal-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}

.modal-btn:disabled::before {
  display: none;
}

/* Legacy Button Classes - KOMPATIBILITA */
.btn-save {
  @extend .modal-btn;
  @extend .modal-btn.success;
}

.btn-cancel {
  @extend .modal-btn;
  @extend .modal-btn.outline;
}

.btn-delete {
  @extend .modal-btn;
  @extend .modal-btn.danger;
}

.btn-secondary {
  @extend .modal-btn;
  @extend .modal-btn.secondary;
}

.btn-primary {
  @extend .modal-btn;
  @extend .modal-btn.primary;
}

/* Specific Modal Button Layouts */
.year-modal .form-actions,
.article-modal .form-actions,
.user-modal .form-actions {
  justify-content: flex-end;
  padding: 2rem;
}

.year-modal .form-actions .modal-btn,
.article-modal .form-actions .modal-btn,
.user-modal .form-actions .modal-btn {
  min-width: 140px;
}

/* Responsive Modal Buttons */
@media (max-width: 768px) {
  .form-actions {
    flex-direction: column;
    gap: 1rem;
    padding: 1.5rem;
  }
  
  .form-actions .right-actions,
  .form-actions .left-actions {
    width: 100%;
    justify-content: center;
    margin-left: 0;
  }
  
  .modal-btn {
    width: 100%;
    justify-content: center;
  }
}

/* Legacy styles that might be used in existing modals */
.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--text-secondary);
  padding: 0.5rem;
  border-radius: var(--radius-md);
  transition: var(--transition-fast);
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
}

.close-button:hover {
  background: var(--bg-terciary);
  color: var(--danger-color);
  transform: scale(1.1);
}
</style>