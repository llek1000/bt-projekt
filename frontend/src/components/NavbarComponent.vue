<template>
  <div>
    <!-- Top Navigation Bar -->
    <div class="topbar">
      <div class="topbar-container">
        <div class="topbar-right">
          <button @click="toggleSearch" class="topbar-button">
            <span class="icon-search">🔍</span>
            <span class="button-text">Search</span>
          </button>
          
          <button @click="toggleQuicklinks" class="topbar-button">
            <span class="icon-quicklinks">≡</span>
            <span class="button-text">Quicklinks</span>
          </button>
          
          <a href="/login" class="topbar-button">
            <span class="icon-login">👤</span>
            <span class="button-text">Login</span>
          </a>
        </div>
      </div>
      
      <!-- Search Dropdown -->
      <div class="search-dropdown" v-if="searchOpen">
        <form class="search-form">
          <input type="text" placeholder="Search for staff and content" class="search-input">
          <button type="submit" class="search-submit">Search</button>
        </form>
      </div>
      
      <!-- Quicklinks Dropdown -->
      <div class="quicklinks-dropdown" v-if="quicklinksOpen">
        <div class="quicklinks-grid">
          <div class="quicklinks-column">
            <h3>Links</h3>
            <ul>
              <li><a href="#">Publications</a></li>
              <li><a href="#">Research Projects</a></li>
              <li><a href="#">Faculty Resources</a></li>
              <li><a href="#">Student Portal</a></li>
            </ul>
          </div>
          <div class="quicklinks-column">
            <h3>Resources</h3>
            <ul>
              <li><a href="#">Library</a></li>
              <li><a href="#">Online Learning</a></li>
              <li><a href="#">Databases</a></li>
              <li><a href="#">Research Tools</a></li>
            </ul>
          </div>
          <div class="quicklinks-column">
            <h3>Contact</h3>
            <ul>
              <li><a href="#">Faculty Directory</a></li>
              <li><a href="#">Department Office</a></li>
              <li><a href="#">Campus Map</a></li>
              <li><a href="#">Support</a></li>
            </ul>
          </div>
        </div>
        <div class="social-media">
          <a href="#" class="social-icon">Facebook</a>
          <a href="#" class="social-icon">Twitter</a>
          <a href="#" class="social-icon">Instagram</a>
          <a href="#" class="social-icon">LinkedIn</a>
        </div>
      </div>
    </div>
    
    <!-- Main Navigation Bar -->
    <nav class="main-navbar">
      <div class="navbar-container">
        <div class="logo-container">
          <a href="/">
            <img src="/src/assets/logo.png" alt="Research Institute Logo" class="logo">
          </a>
        </div>
        
        <div class="main-nav-links">
          <a href="/research" class="main-nav-link">Research</a>
          <a href="/publications" class="main-nav-link">Publications</a>
          <a href="/departments" class="main-nav-link">Departments</a>
          <a href="/about" class="main-nav-link">About</a>
        </div>
      </div>
    </nav>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'NavBar',
  data() {
    return {
      quicklinksOpen: false,
      searchOpen: false
    }
  },
  methods: {
    toggleQuicklinks() {
      this.quicklinksOpen = !this.quicklinksOpen
      if (this.quicklinksOpen) {
        this.searchOpen = false
      }
    },
    toggleSearch() {
      this.searchOpen = !this.searchOpen
      if (this.searchOpen) {
        this.quicklinksOpen = false
      }
    }
  }
})
</script>

<style scoped>
/* Top Navigation Bar */
.topbar {
  background-color: #f5f5f5;
  border-bottom: 1px solid #e0e0e0;
  position: relative;
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
}

.topbar-button {
  display: flex;
  align-items: center;
  background: none;
  border: none;
  padding: 0.5rem 0.75rem;
  color: #555;
  font-size: 0.9rem;
  cursor: pointer;
  text-decoration: none;
}

.topbar-button:hover {
  color: #3498db;
}

.icon-search, .icon-quicklinks, .icon-login {
  margin-right: 0.5rem;
  font-size: 1rem;
}

/* Search Dropdown */
.search-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: white;
  padding: 1rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  z-index: 100;
}

.search-form {
  display: flex;
  max-width: 600px;
  margin: 0 auto;
}

.search-input {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px 0 0 4px;
  font-size: 1rem;
}

.search-submit {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0 1.5rem;
  border-radius: 0 4px 4px 0;
  cursor: pointer;
}

/* Quicklinks Dropdown */
.quicklinks-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: white;
  padding: 2rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  z-index: 100;
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
  color: #3498db;
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
}

.social-icon:hover {
  color: #3498db;
}

/* Main Navigation Bar */
.main-navbar {
  background-color: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

.logo {
  max-height: 60px;
  width: auto;
}

.main-nav-links {
  display: flex;
  gap: 2rem;
}

.main-nav-link {
  text-decoration: none;
  color: #333;
  font-weight: 600;
  font-size: 1.1rem;
  padding: 0.5rem 0;
  transition: color 0.3s ease;
}

.main-nav-link:hover {
  color: #3498db;
}
</style>