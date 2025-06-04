<template>
  <div>
    <div class="topbar">
      <div class="topbar-container">
        <div class="topbar-right">
          <button @click="toggleSearch" class="topbar-button">
            <span class="icon-search">🔍</span>
            <span class="button-text">Vyhľadať</span>
          </button>

          <button @click="toggleQuicklinks" class="topbar-button">
            <span class="icon-quicklinks">≡</span>
            <span class="button-text">Rýchle odkazy</span>
          </button>

          <div v-if="!isAuthenticated" class="auth-section">
            <router-link to="/login" class="topbar-button">
              <span class="icon-login">👤</span>
              <span class="button-text">Prihlásenie</span>
            </router-link>
          </div>

          <div v-else class="auth-section user-menu">
            <div class="user-dropdown" @click="toggleUserMenu">
              <span class="icon-user">👤</span>
              <span class="user-name">{{ user?.username || 'Používateľ' }}</span>
              <span class="dropdown-arrow" :class="{ 'open': userMenuOpen }">▼</span>
            </div>

            <transition name="slide-down">
              <div v-if="userMenuOpen" class="user-dropdown-menu">
                <div class="user-info">
                  <div class="user-details">
                    <div class="user-name-full">{{ user?.username }}</div>
                    <div class="user-email">{{ user?.email }}</div>
                    <div class="user-role" v-if="user?.roles?.length">
                      {{ user.roles.map(r => r.name).join(', ') }}
                    </div>
                  </div>
                </div>

                <div class="menu-divider"></div>

                <div class="menu-actions">
                  <router-link
                    v-if="isAdmin"
                    to="/admin/dashboard"
                    class="menu-item admin-link"
                    @click="closeUserMenu"
                  >
                    <span class="menu-icon">⚙️</span>
                    Admin Dashboard
                  </router-link>

                  <router-link
                    v-if="isEditor"
                    to="/edit/dashboard"
                    class="menu-item editor-link"
                    @click="closeUserMenu"
                  >
                    <span class="menu-icon">✏️</span>
                    Editor Dashboard
                  </router-link>

                  <button @click="handleLogout" class="menu-item logout-button">
                    <span class="menu-icon">🚪</span>
                    Odhlásiť sa
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </div>

      <transition name="slide-down">
        <div class="search-dropdown" v-if="searchOpen">
          <form class="search-form" @submit.prevent="searchArticles">
            <input
              type="text"
              placeholder="Vyhľadajte články..."
              class="search-input"
              v-model="searchQuery"
            >
            <button type="submit" class="search-submit">
              <span>Hľadať</span>
              <i class="search-icon">→</i>
            </button>
          </form>
        </div>
      </transition>

      <transition name="slide-down">
        <div class="quicklinks-dropdown" v-if="quicklinksOpen">
          <div class="quicklinks-grid">
            <div class="quicklinks-column">
              <h3>Výskum</h3>
              <ul>
                <li><a href="/research/life-sciences">Vedy o živote</a></li>
                <li><a href="/research/data-science">Dátová veda</a></li>
                <li><a href="/research/environmental">Environmentálne štúdie</a></li>
                <li><a href="/research/materials">Materiálové vedy</a></li>
              </ul>
            </div>
            <div class="quicklinks-column">
              <h3>Vzdelávanie</h3>
              <ul>
                <li><a href="/programs/undergraduate">Bakalárske štúdium</a></li>
                <li><a href="/programs/graduate">Magisterské štúdium</a></li>
                <li><a href="/programs/phd">Doktorandské štúdium</a></li>
                <li><a href="/programs/continuing">Celoživotné vzdelávanie</a></li>
              </ul>
            </div>
            <div class="quicklinks-column">
              <h3>Zdroje</h3>
              <ul>
                <li><a href="/library">Knižnica</a></li>
                <li><a href="/publications">Publikácie</a></li>
                <li><a href="/events">Udalosti</a></li>
                <li><a href="/contact">Kontakt</a></li>
              </ul>
            </div>
          </div>
          <div class="social-media">
            <a href="#" class="social-icon facebook">📘</a>
            <a href="#" class="social-icon twitter">🐦</a>
            <a href="#" class="social-icon instagram">📷</a>
            <a href="#" class="social-icon linkedin">💼</a>
          </div>
        </div>
      </transition>
    </div>

    <nav class="main-navbar">
      <div class="navbar-container">
        <div class="logo-container">
          <router-link to="/" class="logo-link">
            <div class="logo-wrapper">
              <img src="/logo.png" alt="Institute Logo" class="logo" />
              <div class="logo-text">
                <div class="institute-name">Výskumný inštitút</div>
                <div class="institute-subtitle">Technická univerzita</div>
              </div>
            </div>
          </router-link>
        </div>

        <div class="main-nav-links">
          <router-link to="/" class="main-nav-link">
            Domov
            <div class="nav-underline"></div>
          </router-link>
          <router-link to="/about" class="main-nav-link">
            O nás
            <div class="nav-underline"></div>
          </router-link>
          <router-link to="/departments" class="main-nav-link">
            Oddelenia
            <div class="nav-underline"></div>
          </router-link>
          <router-link to="/publications" class="main-nav-link">
            Publikácie
            <div class="nav-underline"></div>
          </router-link>
        </div>
      </div>
    </nav>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import auth from '@/services/authentification'

export default defineComponent({
  name: 'NavbarComponent',
  data() {
    return {
      quicklinksOpen: false,
      searchOpen: false,
      userMenuOpen: false,
      searchQuery: '',

      isAuthenticated: false,
      user: null as any,
    }
  },

  computed: {
    isAdmin() {
      return this.user?.roles?.some((role: any) => role.name.toLowerCase() === 'admin') || false
    },

    isEditor() {
      return this.user?.roles?.some((role: any) => role.name.toLowerCase() === 'editor') || false
    }
  },

  methods: {
    toggleQuicklinks() {
      this.quicklinksOpen = !this.quicklinksOpen
      if (this.quicklinksOpen) {
        this.searchOpen = false
        this.userMenuOpen = false
      }
    },

    toggleSearch() {
      this.searchOpen = !this.searchOpen
      if (this.searchOpen) {
        this.quicklinksOpen = false
        this.userMenuOpen = false
      }
    },

    toggleUserMenu() {
      this.userMenuOpen = !this.userMenuOpen
      if (this.userMenuOpen) {
        this.quicklinksOpen = false
        this.searchOpen = false
      }
    },

    closeUserMenu() {
      this.userMenuOpen = false
    },

    async searchArticles() {
      if (this.searchQuery.trim()) {
        this.$router.push(`/publications?search=${encodeURIComponent(this.searchQuery)}`)
        this.searchOpen = false
      }
    },

    async handleLogout() {
      try {
        await auth.logout()
        this.isAuthenticated = false
        this.user = null
        this.$router.push('/')
      } catch (error) {
        console.error('Logout error:', error)
        auth.forceLogout()
        this.isAuthenticated = false
        this.user = null
        this.$router.push('/')
      }
    },

    async checkAuthStatus() {
      try {
        const token = localStorage.getItem('token')
        if (!token) {
          this.isAuthenticated = false
          this.user = null
          return
        }

        const response = await fetch('http://localhost/bt/bt-projekt/public/api/user', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })

        if (response.ok) {
          const data = await response.json()
          this.user = data.user
          this.isAuthenticated = true
        } else {
          localStorage.removeItem('token')
          this.isAuthenticated = false
          this.user = null
        }

      } catch (error) {
        console.error('Chyba pri kontrole autentifikácie:', error)
        this.isAuthenticated = false
        this.user = null
      }
    },

    handleClickOutside(event: Event) {
      const target = event.target as HTMLElement

      if (!target.closest('.user-dropdown') && !target.closest('.user-dropdown-menu')) {
        this.userMenuOpen = false
      }

      if (!target.closest('.search-dropdown') && !target.closest('.topbar-button')) {
        this.searchOpen = false
      }

      if (!target.closest('.quicklinks-dropdown') && !target.closest('.topbar-button')) {
        this.quicklinksOpen = false
      }
    }
  },

  async mounted() {
    await this.checkAuthStatus()

    document.addEventListener('click', this.handleClickOutside)
  },

  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },

  watch: {
    '$route'() {
      this.checkAuthStatus()
    }
  }
})
</script>

<style scoped>

:root {
  --primary-color: #2563eb;
  --primary-dark: #1d4ed8;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --border-color: #e5e7eb;
  --light-bg: #f9fafb;
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.topbar {
  background-color: #f5f5f5;
  border-bottom: 1px solid var(--border-color);
  position: relative;
  z-index: 100;
}

.topbar-container {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 0.5rem 1rem;
  max-width: 1200px;
  margin: 0 auto;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.topbar-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: none;
  border: none;
  padding: 0.5rem 0.75rem;
  color: #555;
  font-size: 0.9rem;
  cursor: pointer;
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.topbar-button:hover {
  background-color: rgba(52, 152, 219, 0.1);
  color: #3498db;
}

.icon-search, .icon-quicklinks, .icon-login, .icon-user {
  font-size: 1rem;
}

.auth-section {
  display: flex;
  align-items: center;
}

.user-menu {
  position: relative;
}

.user-dropdown {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: none;
  border: none;
  color: #555;
  font-size: 0.9rem;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.user-dropdown:hover {
  background-color: rgba(52, 152, 219, 0.1);
  color: #3498db;
}

.user-name {
  font-weight: 500;
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.dropdown-arrow {
  font-size: 0.7rem;
  transition: transform 0.2s ease;
}

.dropdown-arrow.open {
  transform: rotate(180deg);
}

.user-dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  box-shadow: var(--shadow-md);
  min-width: 280px;
  z-index: 1000;
  overflow: hidden;
}

.user-info {
  padding: 1rem;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.user-details {
  text-align: left;
}

.user-name-full {
  font-weight: 600;
  font-size: 1rem;
  color: #333;
  margin-bottom: 0.25rem;
}

.user-email {
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.25rem;
}

.user-role {
  font-size: 0.75rem;
  color: #3498db;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.menu-divider {
  height: 1px;
  background: var(--border-color);
}

.menu-actions {
  padding: 0.5rem 0;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  color: #555;
  font-size: 0.9rem;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.menu-item:hover {
  background-color: var(--light-bg);
  color: #333;
}

.menu-icon {
  font-size: 1rem;
  width: 20px;
  text-align: center;
}

.admin-link:hover {
  background-color: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}

.editor-link:hover {
  background-color: rgba(40, 167, 69, 0.1);
  color: #28a745;
}

.logout-button {
  border-top: 1px solid var(--border-color);
  color: #dc3545;
}

.logout-button:hover {
  background-color: rgba(220, 53, 69, 0.1);
  color: #c82333;
}

.search-dropdown, .quicklinks-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: white;
  box-shadow: var(--shadow-md);
  z-index: 99;
}

.search-dropdown {
  padding: 1rem;
}

.search-form {
  display: flex;
  max-width: 600px;
  margin: 0 auto;
}

.search-input {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 4px 0 0 4px;
  font-size: 1rem;
}

.search-submit {
  background-color: var(--primary-color);
  color: white;
  border: none;
  padding: 0 1.5rem;
  border-radius: 0 4px 4px 0;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.search-submit:hover {
  background-color: var(--primary-dark);
}

.quicklinks-dropdown {
  padding: 2rem;
}

.quicklinks-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  max-width: 1000px;
  margin: 0 auto;
}

.quicklinks-column h3 {
  font-size: 1.2rem;
  margin-bottom: 1rem;
  color: #333;
}

.quicklinks-column ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.quicklinks-column li {
  margin-bottom: 0.5rem;
}

.quicklinks-column a {
  color: #555;
  text-decoration: none;
  transition: color 0.3s ease;
}

.quicklinks-column a:hover {
  color: var(--primary-color);
}

.social-media {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
  gap: 1rem;
}

.social-icon {
  color: #555;
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 1.2rem;
}

.social-icon:hover {
  color: var(--primary-color);
}

.main-navbar {
  background-color: white;
  box-shadow: var(--shadow-md);
}

.navbar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  max-width: 1200px;
  margin: 0 auto;
}

.logo-container {
  flex: 0 0 200px;
}

.logo-link {
  text-decoration: none;
}

.logo-wrapper {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  max-height: 60px;
  width: auto;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.institute-name {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--text-primary);
}

.institute-subtitle {
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.main-nav-links {
  display: flex;
  gap: 2rem;
}

.main-nav-link {
  position: relative;
  text-decoration: none;
  color: #333;
  font-weight: 600;
  font-size: 1.1rem;
  padding: 0.5rem 0;
  transition: color 0.3s ease;
}

.main-nav-link:hover,
.main-nav-link.router-link-active {
  color: var(--primary-color);
}

.nav-underline {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: var(--primary-color);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.main-nav-link:hover .nav-underline,
.main-nav-link.router-link-active .nav-underline {
  transform: scaleX(1);
}


.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
  transform-origin: top center;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px) scaleY(0.8);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px) scaleY(0.8);
}


@media (max-width: 768px) {
  .user-name {
    display: none;
  }

  .user-dropdown-menu {
    min-width: 250px;
    right: -10px;
  }

  .quicklinks-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}

@media (max-width: 480px) {
  .button-text {
    display: none;
  }

  .user-dropdown-menu {
    min-width: 220px;
    right: -20px;
  }

  .navbar-container {
    flex-direction: column;
    gap: 1rem;
  }

  .main-nav-links {
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
  }
}
</style>
