<template>
  <div class="dashboard-container">
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-background"></div>
      <div class="hero-overlay"></div>
      <div class="hero-particles"></div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">
            <span class="title-line">Admin</span>
            <span class="title-line highlight">Dashboard</span>
          </h1>
          <p class="hero-subtitle">Správa používateľov, ročníkov konferencie a obsahu</p>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">{{ users.length }}</span>
              <span class="stat-label">Používateľov</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ years.length }}</span>
              <span class="stat-label">Ročníkov</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ assignments.length }}</span>
              <span class="stat-label">Priradení</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Conference Years Management -->
    <div class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa ročníkov konferencie</h2>
          <p class="section-subtitle">Vytvárajte a spravujte ročníky konferencie</p>
        </div>
        
        <div class="management-content">
          <div class="management-actions">
            <button @click="openYearModal()" class="hero-btn primary">
              <span class="icon">+</span>
              Pridať nový ročník
            </button>
          </div>
          
          <!-- Years Grid -->
          <div class="cards-grid">
            <div v-for="year in years" :key="year.id" class="feature-card">
              <div class="card-header">
                <h3>{{ year.semester }} {{ year.year }}</h3>
                <span class="status-badge" :class="{ active: year.is_active }">
                  {{ year.is_active ? 'Aktívny' : 'Neaktívny' }}
                </span>
              </div>
              <div class="card-actions">
                <button @click="openYearModal(year)" class="action-btn edit">Upraviť</button>
                <button @click="deleteYear(year.id)" class="action-btn delete">Zmazať</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Users Management -->
    <div class="management-section alt-bg">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa používateľov</h2>
          <p class="section-subtitle">Spravujte používateľov a ich role</p>
        </div>
        
        <div class="management-content">
          <div class="management-actions">
            <div class="search-container">
              <input 
                v-model="searchQuery" 
                @input="filterUsers" 
                type="text" 
                placeholder="Hľadať používateľa..." 
                class="search-input"
              />
            </div>
            <button @click="showCreateUserModal = true" class="hero-btn primary">
              <span class="icon">+</span>
              Pridať používateľa
            </button>
          </div>

          <!-- Users Table -->
          <div class="table-container">
            <table class="modern-table">
              <thead>
                <tr>
                  <th>Meno</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Akcie</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in filteredUsers" :key="user.id" class="table-row">
                  <td>{{ user.username }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span v-for="role in user.roles" :key="role.id" 
                          class="role-badge" :class="getRoleBadgeClass(role.name)">
                      {{ role.name }}
                    </span>
                  </td>
                  <td>
                    <button @click="openEditUserModal(user)" class="action-btn edit">Upraviť</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Editor Assignments -->
    <div class="management-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Priradenie editorov</h2>
          <p class="section-subtitle">Priraďte editorov k ročníkom konferencie</p>
        </div>
        
        <div class="management-content">
          <div class="assignment-controls">
            <div class="select-group">
              <label>Vyberte ročník:</label>
              <select v-model="selYearForEditors" class="modern-select">
                <option value="">-- Vyberte ročník --</option>
                <option v-for="year in years" :key="year.id" :value="year.id">
                  {{ year.semester }} {{ year.year }}
                </option>
              </select>
            </div>
            
            <div v-if="selYearForEditors" class="select-group">
              <label>Vyberte editora:</label>
              <select v-model="selEditor" class="modern-select">
                <option value="">-- Vyberte editora --</option>
                <option v-for="user in users.filter(u => u.roles.some(r => r.name === 'Editor'))" 
                        :key="user.id" :value="user.id">
                  {{ user.username }}
                </option>
              </select>
              <button @click="assignEditor" class="hero-btn primary" 
                      :disabled="!selEditor">
                Priradiť
              </button>
            </div>
          </div>

          <!-- Assignments List -->
          <div v-if="selYearForEditors" class="assignments-list">
            <div v-for="assignment in assignments" :key="assignment.id" class="assignment-item">
              <div class="assignment-info">
                <span class="editor-name">{{ assignment.user.username }}</span>
                <span class="assignment-date">{{ assignment.created_at }}</span>
              </div>
              <button @click="removeEditor(assignment.id)" class="action-btn delete">
                Odstrániť
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Subpages Management -->
    <div class="management-section alt-bg">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Správa podstránok</h2>
          <p class="section-subtitle">Vytvárajte a spravujte podstránky pre ročníky</p>
        </div>
        
        <div class="management-content">
          <div class="assignment-controls">
            <div class="select-group">
              <label>Vyberte ročník:</label>
              <select v-model="selYearForSubpages" class="modern-select">
                <option value="">-- Vyberte ročník --</option>
                <option v-for="year in years" :key="year.id" :value="year.id">
                  {{ year.semester }} {{ year.year }}
                </option>
              </select>
            </div>
            
            <div v-if="selYearForSubpages">
              <button @click="openSubpageModal()" class="hero-btn primary">
                <span class="icon">+</span>
                Pridať podstránku
              </button>
            </div>
          </div>

          <!-- Subpages Grid -->
          <div v-if="selYearForSubpages" class="subpages-grid">
            <div v-for="subpage in subpages" :key="subpage.id" class="subpage-card">
              <h3>{{ subpage.title }}</h3>
              <p>{{ subpage.content ? subpage.content.substring(0, 100) + '...' : 'Bez obsahu' }}</p>
              <div class="card-actions">
                <button @click="openSubpageModal(subpage)" class="action-btn edit">Upraviť</button>
                <button @click="deleteSubpage(subpage.id)" class="action-btn delete">Zmazať</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create User Modal -->
    <div v-if="showCreateUserModal" class="modal-overlay" @click="closeCreateUserModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>Pridať nového používateľa</h3>
          <button @click="closeCreateUserModal" class="close-button">&times;</button>
        </div>
        <form @submit.prevent="createUser" class="modal-form">
          <div class="form-group">
            <label for="username">Používateľské meno:</label>
            <input 
              type="text" 
              id="username" 
              v-model="formUser.username" 
              class="modern-input" 
              placeholder="Zadajte používateľské meno"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="email">Email:</label>
            <input 
              type="email" 
              id="email" 
              v-model="formUser.email" 
              class="modern-input" 
              placeholder="Zadajte email"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="password">Heslo:</label>
            <input 
              type="password" 
              id="password" 
              v-model="formUser.password" 
              class="modern-input" 
              placeholder="Zadajte heslo"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="password_confirmation">Potvrdiť heslo:</label>
            <input 
              type="password" 
              id="password_confirmation" 
              v-model="formUser.password_confirmation" 
              class="modern-input" 
              placeholder="Potvrďte heslo"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="roles">Role:</label>
            <select id="roles" v-model="formUser.roles" class="modern-input" multiple>
              <option v-for="role in roles" :key="role.id" :value="role.name">
                {{ role.name }}
              </option>
            </select>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="closeCreateUserModal" class="hero-btn secondary">
              Zrušiť
            </button>
            <button type="submit" class="hero-btn primary">
              Pridať používateľa
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Year Form Modal -->
    <div v-if="showYearForm" class="modal-overlay" @click="closeYearModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>{{ editYear ? 'Upraviť ročník' : 'Pridať nový ročník' }}</h3>
          <button @click="closeYearModal" class="close-button">&times;</button>
        </div>
        <form @submit.prevent="saveYear" class="modal-form">
          <div class="form-group">
            <label for="year">Rok:</label>
            <input 
              type="text" 
              id="year" 
              v-model="formYear.year" 
              class="modern-input" 
              placeholder="napr. 2024"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="semester">Semester:</label>
            <select id="semester" v-model="formYear.semester" class="modern-input" required>
              <option value="Winter">Winter</option>
              <option value="Summer">Summer</option>
            </select>
          </div>
          
          <div class="form-group">
            <div class="checkbox-group">
              <label>
                <input type="checkbox" v-model="formYear.is_active" />
                Aktívny ročník
              </label>
            </div>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="closeYearModal" class="hero-btn secondary">
              Zrušiť
            </button>
            <button type="submit" class="hero-btn primary">
              {{ editYear ? 'Upraviť' : 'Pridať' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Subpage Form Modal -->
    <div v-if="showSubpageForm" class="modal-overlay" @click="closeSubpageModal">
      <div class="modal-container large" @click.stop>
        <div class="modal-header">
          <h3>{{ editSubpage ? 'Upraviť podstránku' : 'Pridať novú podstránku' }}</h3>
          <button @click="closeSubpageModal" class="close-button">&times;</button>
        </div>
        <form @submit.prevent="saveSubpage" class="modal-form">
          <div class="form-group">
            <label for="title">Názov:</label>
            <input 
              type="text" 
              id="title" 
              v-model="formSubpage.title" 
              class="modern-input" 
              placeholder="Názov podstránky"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="content">Obsah:</label>
            <Editor
              v-model="formSubpage.content"
              :init="{
                height: 400,
                menubar: false,
                plugins: 'lists link image table code help wordcount',
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
              }"
            />
          </div>
          
          <div class="form-actions">
            <button type="button" @click="closeSubpageModal" class="hero-btn secondary">
              Zrušiť
            </button>
            <button type="submit" class="hero-btn primary">
              {{ editSubpage ? 'Upraviť' : 'Pridať' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import Editor from '@tinymce/tinymce-vue'
import adminPanel from '@/services/adminPanel'

const years = ref<any[]>([])
const users = ref<any[]>([]), roles = ref<any[]>([])
const editors = ref<any[]>([])
const assignments = ref<any[]>([]), subpages = ref<any[]>([])
const selYearForEditors = ref<number|null>(null), selEditor = ref<number|null>(null)
const selYearForSubpages = ref<number|null>(null)

const showYearForm = ref(false), editYear = ref<any>(null)
const formYear = ref({ semester: 'Winter', year: '', is_active: false })

const showSubpageForm = ref(false), editSubpage = ref<any>(null)
const formSubpage = ref({ title: '', content: '' })

const searchQuery = ref(''), showCreateUserModal = ref(false), showEditUserModal = ref(false)
const showDeleteConfirmModal = ref(false), showPasswordChangeModal = ref(false), selectedUser = ref<any>(null)

const formUser = ref({ 
  username: '', 
  email: '', 
  password: '', 
  password_confirmation: '', 
  roles: [] 
})

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  return users.value.filter(user => 
    user.username.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

function getRoleBadgeClass(role: string) { 
  return role.toLowerCase() === 'admin' ? 'admin' : 'editor'
}

function filterUsers() { /* computed handles this */ }

async function refreshUsers() { 
  const res = await adminPanel.getUsers()
  users.value = res.data.users || []
}

async function fetchRoles() { 
  const res = await adminPanel.getRoles()
  roles.value = res.data.roles || []
}

async function refreshYears(){
  const res = await adminPanel.getConferenceYears()
  years.value = res.data.data
}

async function refreshEditors(){
  const res = await adminPanel.getUsers()
  editors.value = res.data.users?.filter(u => u.roles.some(r => r.name.toLowerCase() === 'editor')) || []
}

async function refreshAssignments(){
  if(selYearForEditors.value) {
    const res = await adminPanel.listYearEditors(selYearForEditors.value)
    assignments.value = res.data
  }
}

async function refreshSubpages(){
  if(selYearForSubpages.value) {
    const res = await adminPanel.listSubpages(selYearForSubpages.value)
    subpages.value = res.data.data
  }
}

function openYearModal(y = null){
  editYear.value = y
  formYear.value = y ? { ...y } : { semester:'Winter', year:'', is_active:false }
  showYearForm.value = true
}

function closeYearModal(){ 
  showYearForm.value = false
  editYear.value = null 
}

async function saveYear(){
  if(editYear.value?.id)
    await adminPanel.updateYear(editYear.value.id, formYear.value)
  else
    await adminPanel.createYear(formYear.value)
  closeYearModal()
  refreshYears()
}

async function deleteYear(id: number){
  await adminPanel.deleteYear(id)
  refreshYears()
}

async function assignEditor(){
  if(selYearForEditors.value && selEditor.value)
    await adminPanel.assignEditor(selYearForEditors.value, selEditor.value)
  refreshAssignments()
}

async function removeEditor(id: number){
  if(selYearForEditors.value)
    await adminPanel.removeEditor(selYearForEditors.value, id)
  refreshAssignments()
}

function openSubpageModal(s = null){
  editSubpage.value = s
  formSubpage.value = s ? { title: s.title, content: s.content } : { title: '', content: '' }
  showSubpageForm.value = true
}

function closeSubpageModal(){ 
  showSubpageForm.value = false
  editSubpage.value = null 
}

async function saveSubpage(){
  if(!selYearForSubpages.value) return
  if(editSubpage.value?.id)
    await adminPanel.updateSubpage(editSubpage.value.id, formSubpage.value)
  else
    await adminPanel.createSubpage(selYearForSubpages.value, formSubpage.value)
  closeSubpageModal()
  refreshSubpages()
}

async function deleteSubpage(id: number){
  await adminPanel.deleteSubpage(id)
  refreshSubpages()
}

// User modal handlers (keep existing functionality)
function openEditUserModal(user: any) { selectedUser.value = user; showEditUserModal.value = true }
function handleUserCreated() { refreshUsers(); showCreateUserModal.value = false }
function handleUserUpdated() { refreshUsers(); showEditUserModal.value = false }
function handleUserDeleted() { refreshUsers(); showDeleteConfirmModal.value = false; showEditUserModal.value = false }
function handlePasswordUpdated() { showPasswordChangeModal.value = false }

function closeCreateUserModal() {
  showCreateUserModal.value = false
  formUser.value = { username: '', email: '', password: '', password_confirmation: '', roles: [] }
}

async function createUser() {
  try {
    await adminPanel.createUser(formUser.value)
    closeCreateUserModal()
    refreshUsers()
  } catch (error) {
    console.error('Error creating user:', error)
  }
}

watch(selYearForEditors, refreshAssignments)
watch(selYearForSubpages, refreshSubpages)

onMounted(async()=>{
  await Promise.all([
    refreshUsers(),
    fetchRoles(), 
    refreshYears(),
    refreshEditors()
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
  max-width: 400px;
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

/* Tables */
.table-container {
  background: var(--white);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}

.modern-table th {
  background-color: var(--light-bg);
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
  background-color: var(--light-bg);
}

.role-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
  margin-right: 0.5rem;
}

.role-badge.admin {
  background-color: #fef3c7;
  color: #92400e;
}

.role-badge.editor {
  background-color: #dbeafe;
  color: #1e40af;
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

/* Assignment Controls */
.assignment-controls {
  display: flex;
  gap: 2rem;
  align-items: end;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.select-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.select-group label {
  font-weight: 500;
  color: var(--text-primary);
}

.modern-select {
  padding: 0.75rem;
  border: 2px solid #000000; /* Changed to black */
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

/* Assignments List */
.assignments-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.assignment-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: var(--light-bg);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.assignment-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.editor-name {
  font-weight: 600;
  color: var(--text-primary);
}

.assignment-date {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

/* Subpages Grid */
.subpages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
}

.subpage-card {
  background: var(--white);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 1.5rem;
  transition: all 0.3s ease;
}

.subpage-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.subpage-card h3 {
  margin: 0 0 1rem 0;
  color: var(--text-primary);
  font-size: 1.1rem;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
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

.modal-container.large {
  max-width: 800px;
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
  border: 2px solid #000000; /* Changed to black */
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

.checkbox-group label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-group input[type="checkbox"] {
  width: auto;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
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
  
  .assignment-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .cards-grid {
    grid-template-columns: 1fr;
  }
  
  .assignment-item {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
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
  
  .form-actions {
    flex-direction: column;
  }
}
</style>