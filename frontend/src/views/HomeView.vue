<template>
  <div class="page-container">
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
      <transition name="slide-down">
        <div class="search-dropdown" v-if="searchOpen">
          <form class="search-form">
            <input type="text" placeholder="Search for staff and content" class="search-input">
            <button type="submit" class="search-submit">
              <span>Search</span>
              <i class="search-icon">→</i>
            </button>
          </form>
        </div>
      </transition>
      
      <!-- Quicklinks Dropdown -->
      <transition name="slide-down">
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
            <a href="#" class="social-icon facebook">📘</a>
            <a href="#" class="social-icon twitter">🐦</a>
            <a href="#" class="social-icon instagram">📷</a>
            <a href="#" class="social-icon linkedin">💼</a>
          </div>
        </div>
      </transition>
    </div>
    
    <!-- Main Navigation Bar -->
    <nav class="main-navbar">
      <div class="navbar-container">
        <div class="logo-container">
          <a href="/" class="logo-link">
            <div class="logo-wrapper">
              <img src="/src/assets/logo.png" alt="Research Institute Logo" class="logo">
              <div class="logo-text">
                <span class="institute-name">Research Institute</span>
                <span class="institute-subtitle">Excellence in Science</span>
              </div>
            </div>
          </a>
        </div>
        
        <div class="main-nav-links">
          <a href="/research" class="main-nav-link">
            <span>Research</span>
            <div class="nav-underline"></div>
          </a>
          <a href="/publications" class="main-nav-link">
            <span>Publications</span>
            <div class="nav-underline"></div>
          </a>
          <a href="/departments" class="main-nav-link">
            <span>Departments</span>
            <div class="nav-underline"></div>
          </a>
          <a href="/about" class="main-nav-link">
            <span>About</span>
            <div class="nav-underline"></div>
          </a>
        </div>
      </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
      <!-- Hero Section -->
      <section class="hero-section">
        <div class="hero-background">
          <div class="hero-overlay"></div>
          <div class="hero-particles"></div>
        </div>
        <div class="hero-content">
          <div class="hero-text">
            <h1 class="hero-title">
              <span class="title-line">Center for</span>
              <span class="title-line highlight">Scientific Research</span>
            </h1>
            <p class="hero-subtitle">Advancing knowledge through innovation and collaboration</p>
            <div class="hero-buttons">
              <a href="/research" class="hero-btn primary">
                <span>Explore Research</span>
                <i class="btn-arrow">→</i>
              </a>
              <a href="/about" class="hero-btn secondary">
                <span>Learn More</span>
              </a>
            </div>
          </div>
          <div class="hero-stats">
            <div class="stat-item">
              <div class="stat-number">150+</div>
              <div class="stat-label">Researchers</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">50+</div>
              <div class="stat-label">Projects</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">500+</div>
              <div class="stat-label">Publications</div>
            </div>
          </div>
        </div>
        <div class="scroll-indicator">
          <span>Scroll to explore</span>
          <div class="scroll-arrow">↓</div>
        </div>
      </section>
      
      <!-- Welcome Section -->
      <section class="welcome-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Welcome to the Future of Research</h2>
            <p class="section-subtitle">Where innovation meets excellence</p>
          </div>
          <div class="welcome-content">
            <div class="welcome-text">
              <p class="highlight-text">
                Our institute is dedicated to advancing scientific knowledge through cutting-edge research across multiple disciplines.
              </p>
              <p>
                We bring together world-class researchers, state-of-the-art facilities, and innovative approaches to tackle the most pressing challenges of our time.
              </p>
              <p>
                Through collaborative partnerships with industry, government, and other academic institutions, we strive to translate scientific discoveries 
                into real-world applications that benefit society and improve lives.
              </p>
            </div>
            <div class="welcome-features">
              <div class="feature-item">
                <div class="feature-icon">🎯</div>
                <h4>Focused Research</h4>
                <p>Targeted studies in key scientific areas</p>
              </div>
              <div class="feature-item">
                <div class="feature-icon">🤝</div>
                <h4>Collaboration</h4>
                <p>Global partnerships and knowledge sharing</p>
              </div>
              <div class="feature-item">
                <div class="feature-icon">💡</div>
                <h4>Innovation</h4>
                <p>Cutting-edge technologies and methods</p>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Research Areas Section -->
      <section class="research-areas">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Our Research Areas</h2>
            <p class="section-subtitle">Pioneering discoveries across multiple disciplines</p>
          </div>
          <div class="research-grid">
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon life-sciences">🧪</div>
                <h3>Life Sciences</h3>
              </div>
              <p>Investigating fundamental biological processes and developing new approaches to health and disease.</p>
              <div class="card-footer">
                <a href="#" class="learn-more">Learn More →</a>
              </div>
            </div>
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon data-science">💻</div>
                <h3>Data Science</h3>
              </div>
              <p>Developing computational methods and AI algorithms to extract insights from complex datasets.</p>
              <div class="card-footer">
                <a href="#" class="learn-more">Learn More →</a>
              </div>
            </div>
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon environmental">🌍</div>
                <h3>Environmental Science</h3>
              </div>
              <p>Studying climate change, biodiversity, and developing sustainable solutions for environmental challenges.</p>
              <div class="card-footer">
                <a href="#" class="learn-more">Learn More →</a>
              </div>
            </div>
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon materials">⚡</div>
                <h3>Materials Science</h3>
              </div>
              <p>Creating and analyzing new materials with applications in energy, healthcare, and technology.</p>
              <div class="card-footer">
                <a href="#" class="learn-more">Learn More →</a>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Latest News Section -->
      <section class="latest-news">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Latest News & Discoveries</h2>
            <p class="section-subtitle">Stay updated with our groundbreaking research</p>
          </div>
          <div class="news-grid">
            <article class="news-card featured">
              <div class="news-image">
                <div class="news-category">Breakthrough</div>
              </div>
              <div class="news-content">
                <div class="news-meta">
                  <span class="news-date">May 18, 2025</span>
                  <span class="reading-time">5 min read</span>
                </div>
                <h3>Major Breakthrough in Quantum Computing</h3>
                <p>Our researchers have achieved a significant milestone in quantum error correction, bringing practical quantum computing closer to reality.</p>
                <a href="#" class="read-more">Read Full Story →</a>
              </div>
            </article>
            <article class="news-card">
              <div class="news-content">
                <div class="news-meta">
                  <span class="news-date">May 5, 2025</span>
                  <span class="reading-time">3 min read</span>
                </div>
                <h3>New International Research Collaboration</h3>
                <p>The institute has established a new partnership with leading research centers in Europe and Asia.</p>
                <a href="#" class="read-more">Read More →</a>
              </div>
            </article>
            <article class="news-card">
              <div class="news-content">
                <div class="news-meta">
                  <span class="news-date">April 22, 2025</span>
                  <span class="reading-time">2 min read</span>
                </div>
                <h3>Annual Science Symposium Announced</h3>
                <p>The 15th Annual Science Symposium will be held this October, featuring keynote speakers from around the world.</p>
                <a href="#" class="read-more">Read More →</a>
              </div>
            </article>
          </div>
        </div>
      </section>
      
      <!-- Events & Department Highlights -->
      <section class="events-departments">
        <div class="container">
          <div class="section-grid">
            <!-- Upcoming Events -->
            <div class="events-section">
              <h2 class="section-title">Upcoming Events</h2>
              <div class="events-list">
                <div class="event-card">
                  <div class="event-date">
                    <span class="date-number">12</span>
                    <span class="date-month">JUN</span>
                  </div>
                  <div class="event-details">
                    <h4>Research Methodology Workshop</h4>
                    <p>Advanced research methodologies and data analysis techniques.</p>
                    <div class="event-meta">
                      <span class="event-time">⏰ 10:00 AM - 4:00 PM</span>
                      <span class="event-location">📍 Science Building</span>
                    </div>
                  </div>
                </div>
                <div class="event-card">
                  <div class="event-date">
                    <span class="date-number">24</span>
                    <span class="date-month">JUL</span>
                  </div>
                  <div class="event-details">
                    <h4>Future of Renewable Energy</h4>
                    <p>Guest lecture by Dr. Elena Rodriguez from MIT.</p>
                    <div class="event-meta">
                      <span class="event-time">⏰ 2:00 PM - 4:00 PM</span>
                      <span class="event-location">📍 Main Auditorium</span>
                    </div>
                  </div>
                </div>
              </div>
              <a href="/events" class="view-all-btn">View All Events →</a>
            </div>
            
            <!-- Department Highlights -->
            <div class="departments-section">
              <h2 class="section-title">Our Departments</h2>
              <div class="departments-grid">
                <a href="/departments/animal-science" class="department-card">
                  <div class="department-icon">🐄</div>
                  <h4>Animal Science</h4>
                  <p>Sustainable farming practices</p>
                </a>
                <a href="/departments/biochemistry" class="department-card">
                  <div class="department-icon">🧬</div>
                  <h4>Biochemistry</h4>
                  <p>Chemical processes in life</p>
                </a>
                <a href="/departments/physics" class="department-card">
                  <div class="department-icon">⚛️</div>
                  <h4>Physics</h4>
                  <p>Fundamental principles</p>
                </a>
                <a href="/departments/earth-sciences" class="department-card">
                  <div class="department-icon">🌋</div>
                  <h4>Earth Sciences</h4>
                  <p>Planet's characteristics</p>
                </a>
              </div>
              <a href="/departments" class="view-all-btn">View All Departments →</a>
            </div>
          </div>
        </div>
      </section>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="container">
          <div class="footer-grid">
            <div class="footer-column main">
              <div class="footer-logo">
                <h3>Research Institute</h3>
                <p class="footer-tagline">Excellence in Science & Innovation</p>
              </div>
              <div class="contact-info">
                <div class="contact-item">
                  <span class="contact-icon">📍</span>
                  <span>University Campus, 1180 Vienna, Austria</span>
                </div>
                <div class="contact-item">
                  <span class="contact-icon">📞</span>
                  <span>+43 1 47654 0</span>
                </div>
                <div class="contact-item">
                  <span class="contact-icon">✉️</span>
                  <span>research@institute.ac.at</span>
                </div>
              </div>
            </div>
            <div class="footer-column">
              <h4>Quick Links</h4>
              <ul>
                <li><a href="/research">Research</a></li>
                <li><a href="/publications">Publications</a></li>
                <li><a href="/departments">Departments</a></li>
                <li><a href="/about">About Us</a></li>
                <li><a href="/contact">Contact</a></li>
              </ul>
            </div>
            <div class="footer-column">
              <h4>Connect With Us</h4>
              <div class="social-links">
                <a href="#" class="social-link facebook">📘 Facebook</a>
                <a href="#" class="social-link twitter">🐦 Twitter</a>
                <a href="#" class="social-link linkedin">💼 LinkedIn</a>
                <a href="#" class="social-link youtube">📺 YouTube</a>
              </div>
              <div class="newsletter">
                <h5>Newsletter</h5>
                <p>Stay updated with our latest research</p>
                <div class="newsletter-form">
                  <input type="email" placeholder="Your email">
                  <button>Subscribe</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="container">
          <p>© 2025 Research Institute. All rights reserved.</p>
          <div class="footer-links">
            <a href="/privacy">Privacy Policy</a>
            <a href="/terms">Terms of Use</a>
            <a href="/sitemap">Sitemap</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'HomeView',
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
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

/* CSS Variables */
:root {
  --primary-color: #2563eb;
  --primary-dark: #1d4ed8;
  --primary-light: #3b82f6;
  --secondary-color: #f59e0b;
  --accent-color: #10b981;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --text-light: #9ca3af;
  --bg-primary: #ffffff;
  --bg-secondary: #f8fafc;
  --bg-dark: #0f172a;
  --border-color: #e5e7eb;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

/* Base Styles */
* {
  box-sizing: border-box;
}

.page-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: 'Inter', sans-serif;
  color: var(--text-primary);
  line-height: 1.6;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Transitions */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Top Navigation Bar */
.topbar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
}

.topbar-container {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 0.75rem 1.5rem;
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
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  padding: 0.5rem 1rem;
  color: white;
  font-size: 0.875rem;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.topbar-button:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-1px);
}

.icon-search, .icon-quicklinks, .icon-login {
  margin-right: 0.5rem;
  font-size: 1rem;
}

/* Search & Quicklinks Dropdowns */
.search-dropdown, .quicklinks-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border-color);
  box-shadow: var(--shadow-lg);
  z-index: 100;
}

.search-dropdown {
  padding: 1.5rem;
}

.search-form {
  display: flex;
  max-width: 600px;
  margin: 0 auto;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

.search-input {
  flex: 1;
  padding: 1rem 1.5rem;
  border: none;
  font-size: 1rem;
  outline: none;
  background: white;
}

.search-submit {
  background: var(--gradient-primary);
  color: white;
  border: none;
  padding: 0 2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.search-submit:hover {
  transform: scale(1.05);
}

.quicklinks-dropdown {
  padding: 2rem;
}

.quicklinks-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 3rem;
  max-width: 1000px;
  margin: 0 auto 2rem;
}

.quicklinks-column h3 {
  font-size: 1.25rem;
  margin-bottom: 1rem;
  color: var(--text-primary);
  font-weight: 600;
}

.quicklinks-column ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.quicklinks-column li {
  margin-bottom: 0.75rem;
}

.quicklinks-column a {
  color: var(--text-secondary);
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-block;
}

.quicklinks-column a:hover {
  color: var(--primary-color);
  transform: translateX(5px);
}

.social-media {
  display: flex;
  justify-content: center;
  gap: 1rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
}

.social-icon {
  padding: 0.75rem;
  border-radius: 12px;
  background: var(--bg-secondary);
  text-decoration: none;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.social-icon:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
}

.social-icon.facebook:hover { background: #1877f2; color: white; }
.social-icon.twitter:hover { background: #1da1f2; color: white; }
.social-icon.instagram:hover { background: #e4405f; color: white; }
.social-icon.linkedin:hover { background: #0077b5; color: white; }

/* Main Navigation */
.main-navbar {
  background: white;
  box-shadow: var(--shadow-md);
  position: sticky;
  top: 0;
  z-index: 50;
}

.navbar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}

.logo-container {
  flex: 0 0 auto;
}

.logo-link {
  text-decoration: none;
  color: inherit;
}

.logo-wrapper {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  max-height: 50px;
  width: auto;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.institute-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  font-family: 'Poppins', sans-serif;
}

.institute-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  font-weight: 400;
}

.main-nav-links {
  display: flex;
  gap: 2rem;
}

.main-nav-link {
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
  font-size: 1.1rem;
  position: relative;
  padding: 0.5rem 0;
  transition: color 0.3s ease;
}

.main-nav-link:hover {
  color: var(--primary-color);
}

.nav-underline {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: var(--gradient-primary);
  transition: width 0.3s ease;
}

.main-nav-link:hover .nav-underline {
  width: 100%;
}

/* Hero Section */
.hero-section {
  position: relative;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: var(--gradient-primary);
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="500" cy="500" r="500"><stop offset="0%" stop-color="%234facfe"/><stop offset="100%" stop-color="%2300f2fe"/></radialGradient></defs><rect width="1000" height="1000" fill="url(%23a)"/></svg>') center/cover;
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
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1" fill="white" opacity="0.3"><animate attributeName="opacity" values="0.3;1;0.3" dur="2s" repeatCount="indefinite"/></circle><circle cx="80" cy="40" r="1" fill="white" opacity="0.4"><animate attributeName="opacity" values="0.4;1;0.4" dur="3s" repeatCount="indefinite"/></circle><circle cx="40" cy="80" r="1" fill="white" opacity="0.2"><animate attributeName="opacity" values="0.2;1;0.2" dur="4s" repeatCount="indefinite"/></circle></svg>') repeat;
}

.hero-content {
  position: relative;
  z-index: 10;
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 2rem;
  animation: fadeInUp 1s ease-out;
}

.hero-title {
  font-family: 'Poppins', sans-serif;
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 700;
  color: white;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.title-line {
  display: block;
}

.title-line.highlight {
  background: linear-gradient(45deg, #ffd700, #ff6b6b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 1.5rem;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 3rem;
  font-weight: 300;
}

.hero-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 4rem;
}

.hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem 2rem;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.hero-btn.primary {
  background: white;
  color: var(--primary-color);
  box-shadow: var(--shadow-lg);
}

.hero-btn.primary:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-xl);
}

.hero-btn.secondary {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(10px);
}

.hero-btn.secondary:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-3px);
}

.btn-arrow {
  transition: transform 0.3s ease;
}

.hero-btn:hover .btn-arrow {
  transform: translateX(5px);
}

.hero-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  max-width: 600px;
  margin: 0 auto;
}

.stat-item {
  text-align: center;
  color: white;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 700;
  font-family: 'Poppins', sans-serif;
}

.stat-label {
  font-size: 1rem;
  opacity: 0.9;
  font-weight: 300;
}

.scroll-indicator {
  position: absolute;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  color: white;
  text-align: center;
  animation: float 2s ease-in-out infinite;
}

.scroll-arrow {
  font-size: 1.5rem;
  margin-top: 0.5rem;
  animation: pulse 2s ease-in-out infinite;
}

/* Section Headers */
.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-title {
  font-family: 'Poppins', sans-serif;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.25rem;
  color: var(--text-secondary);
  font-weight: 300;
}

/* Welcome Section */
.welcome-section {
  padding: 6rem 0;
  background: var(--bg-primary);
}

.welcome-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 4rem;
  align-items: start;
}

.welcome-text {
  font-size: 1.125rem;
  line-height: 1.8;
}

.highlight-text {
  font-size: 1.25rem;
  font-weight: 500;
  color: var(--primary-color);
  margin-bottom: 1.5rem;
}

.welcome-features {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.feature-item {
  padding: 1.5rem;
  background: var(--bg-secondary);
  border-radius: 16px;
  text-align: center;
  transition: all 0.3s ease;
}

.feature-item:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
}

.feature-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.feature-item h4 {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--text-primary);
}

.feature-item p {
  color: var(--text-secondary);
  font-size: 0.875rem;
}

/* Research Areas */
.research-areas {
  padding: 6rem 0;
  background: var(--bg-secondary);
}

.research-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.research-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  position: relative;
  overflow: hidden;
}

.research-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-primary);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.research-card:hover::before {
  transform: scaleX(1);
}

.research-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.research-icon {
  font-size: 2.5rem;
  padding: 1rem;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.research-icon.life-sciences { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.research-icon.data-science { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.research-icon.environmental { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.research-icon.materials { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.research-card h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.research-card p {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.card-footer {
  margin-top: auto;
}

.learn-more {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.learn-more:hover {
  gap: 1rem;
}

/* Latest News */
.latest-news {
  padding: 6rem 0;
  background: var(--bg-primary);
}

.news-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

.news-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
}

.news-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.news-card.featured {
  grid-row: span 2;
}

.news-image {
  position: relative;
  height: 200px;
  background: var(--gradient-primary);
  display: flex;
  align-items: center;
  justify-content: center;
}

.news-card.featured .news-image {
  height: 300px;
}

.news-category {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
  backdrop-filter: blur(10px);
}

.news-content {
  padding: 1.5rem;
}

.news-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  font-size: 0.875rem;
  color: var(--text-light);
}

.news-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
  line-height: 1.4;
}

.news-card.featured h3 {
  font-size: 1.5rem;
}

.news-card p {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1rem;
}

.read-more {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.3s ease;
}

.read-more:hover {
  color: var(--primary-dark);
}

/* Events & Departments Section */
.events-departments {
  padding: 6rem 0;
  background: var(--bg-secondary);
}

.section-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
}

.events-section, .departments-section {
  background: white;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: var(--shadow-sm);
}

.events-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.event-card {
  display: flex;
  gap: 1rem;
  padding: 1.5rem;
  background: var(--bg-secondary);
  border-radius: 16px;
  transition: all 0.3s ease;
}

.event-card:hover {
  transform: translateX(5px);
  box-shadow: var(--shadow-md);
}

.event-date {
  background: var(--gradient-primary);
  color: white;
  padding: 1rem;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 80px;
  text-align: center;
}

.date-number {
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
}

.date-month {
  font-size: 0.875rem;
  text-transform: uppercase;
  margin-top: 0.25rem;
}

.event-details h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.event-details p {
  color: var(--text-secondary);
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.event-meta {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  color: var(--text-light);
  font-size: 0.75rem;
}

.departments-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.department-card {
  background: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: 16px;
  text-align: center;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s ease;
  display: block;
}

.department-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
  background: white;
}

.department-icon {
  font-size: 2rem;
  margin-bottom: 0.75rem;
}

.department-card h4 {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.department-card p {
  color: var(--text-secondary);
  font-size: 0.875rem;
}

.view-all-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border: 2px solid var(--primary-color);
  border-radius: 50px;
  transition: all 0.3s ease;
}

.view-all-btn:hover {
  background: var(--primary-color);
  color: white;
  transform: translateY(-2px);
}

/* Footer */
.footer {
  background: var(--bg-dark);
  color: white;
  margin-top: auto;
}

.footer-content {
  padding: 4rem 0 2rem;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 3rem;
}

.footer-column.main {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.footer-logo h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.footer-tagline {
  color: #94a3b8;
  font-size: 0.875rem;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #cbd5e1;
  font-size: 0.875rem;
}

.contact-icon {
  font-size: 1rem;
}

.footer-column h4 {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: white;
}

.footer-column ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.footer-column a {
  color: #94a3b8;
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 0.875rem;
}

.footer-column a:hover {
  color: white;
}

.social-links {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 2rem;
}

.social-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #94a3b8;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 0.875rem;
}

.social-link:hover {
  color: white;
  transform: translateX(5px);
}

.newsletter h5 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: white;
}

.newsletter p {
  color: #94a3b8;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.newsletter-form {
  display: flex;
  gap: 0.5rem;
}

.newsletter-form input {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #374151;
  border-radius: 8px;
  background: #1f2937;
  color: white;
  font-size: 0.875rem;
  outline: none;
}

.newsletter-form input::placeholder {
  color: #6b7280;
}

.newsletter-form button {
  padding: 0.75rem 1rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.newsletter-form button:hover {
  background: var(--primary-dark);
}

.footer-bottom {
  background: #0f172a;
  padding: 1.5rem 0;
  border-top: 1px solid #374151;
}

.footer-bottom .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.footer-bottom p {
  margin: 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.footer-links {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.footer-links a {
  color: #6b7280;
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: white;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .welcome-content {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
  
  .section-grid {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
  
  .news-grid {
    grid-template-columns: 1fr;
  }
  
  .departments-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .topbar-right {
    flex-wrap: wrap;
    gap: 0.25rem;
  }
  
  .topbar-button .button-text {
    display: none;
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
  
  .logo-wrapper {
    flex-direction: column;
    text-align: center;
    gap: 0.5rem;
  }
  
  .hero-content {
    padding: 0 1rem;
  }
  
  .hero-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .hero-stats {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .quicklinks-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .research-grid {
    grid-template-columns: 1fr;
  }
  
  .event-card {
    flex-direction: column;
    text-align: center;
  }
  
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .footer-bottom .container {
    flex-direction: column;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 1rem;
  }
  
  .hero-section {
    height: 80vh;
  }
  
  .section-header {
    margin-bottom: 2rem;
  }
  
  .welcome-section,
  .research-areas,
  .latest-news,
  .events-departments {
    padding: 3rem 0;
  }
  
  .events-section,
  .departments-section {
    padding: 1.5rem;
  }
}
</style>