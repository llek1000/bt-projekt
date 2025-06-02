<template>
  <div class="page-container">
    <!-- Horná lišta -->
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
          
          <a href="/login" class="topbar-button">
            <span class="icon-login">👤</span>
            <span class="button-text">Prihlásenie</span>
          </a>
        </div>
      </div>
      
      <!-- Dropdown vyhľadávania -->
      <transition name="slide-down">
        <div class="search-dropdown" v-if="searchOpen">
          <form class="search-form">
            <input type="text" placeholder="Vyhľadajte zamestnancov a obsah" class="search-input">
            <button type="submit" class="search-submit">
              <span>Hľadať</span>
              <i class="search-icon">→</i>
            </button>
          </form>
        </div>
      </transition>
      
      <!-- Dropdown rýchlych odkazov -->
      <transition name="slide-down">
        <div class="quicklinks-dropdown" v-if="quicklinksOpen">
          <div class="quicklinks-grid">
            <div class="quicklinks-column">
              <h3>Odkazy</h3>
              <ul>
                <li><a href="#">Výskumné projekty</a></li>
                <li><a href="#">Materiály fakulty</a></li>
                <li><a href="#">Portál študentov</a></li>
              </ul>
            </div>
            <div class="quicklinks-column">
              <h3>Zdroje</h3>
              <ul>
                <li><a href="#">Databázy</a></li>
                <li><a href="#">Nástroje pre výskum</a></li>
              </ul>
            </div>
            <div class="quicklinks-column">
              <h3>Kontakt</h3>
              <ul>
                <li><a href="#">Adresár fakulty</a></li>
                <li><a href="#">Kancelária oddelenia</a></li>
                <li><a href="#">Mapa areálu</a></li>
                <li><a href="#">Podpora</a></li>
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
    
    <!-- Hlavné menu -->
    <nav class="main-navbar">
      <div class="navbar-container">
        <div class="logo-container">
          <a href="/" class="logo-link">
            <div class="logo-wrapper">
              <img src="/src/assets/logo.png" alt="Logo výskumného inštitútu" class="logo">
              <div class="logo-text">
                <span class="institute-name">Výskumný inštitút</span>
                <span class="institute-subtitle">Excelencia vo vede</span>
              </div>
            </div>
          </a>
        </div>
        
        <div class="main-nav-links">
          <a href="/publications" class="main-nav-link">
            <span>Publikácie</span>
            <div class="nav-underline"></div>
          </a>
          <a href="/departments" class="main-nav-link active">
            <span>Oddelenia</span>
            <div class="nav-underline"></div>
          </a>
          <a href="/about" class="main-nav-link">
            <span>O nás</span>
            <div class="nav-underline"></div>
          </a>
        </div>
      </div>
    </nav>
    
    <!-- Hlavný obsah -->
    <main class="main-content">
      <!-- Hero sekcia -->
      <section class="hero-section">
        <div class="hero-background">
          <div class="hero-overlay"></div>
          <div class="hero-particles"></div>
        </div>
        <div class="hero-content">
          <div class="hero-text">
            <h1 class="hero-title">
              <span class="title-line">Naše</span>
              <span class="title-line highlight">Oddelenia</span>
            </h1>
            <p class="hero-subtitle">
              Preskúmajte rozmanité výskumné oblasti a špecializácie našich oddelení
            </p>
          </div>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">12</span>
              <span class="stat-label">Oddelení</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">150+</span>
              <span class="stat-label">Výskumníkov</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">50+</span>
              <span class="stat-label">Projektov</span>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Oddelenia Grid -->
      <section class="departments-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Výskumné oddelenia</h2>
            <p class="section-subtitle">Každé oddelenie sa špecializuje na špecifické oblasti výskumu</p>
          </div>
          
          <div class="departments-grid">
            <div class="department-card">
              <div class="card-header">
                <div class="department-icon biology">🧬</div>
                <h3>Biológia a biotechnológie</h3>
              </div>
              <p class="department-description">
                Výskum v oblasti molekulárnej biológie, genetiky a biotechnologických aplikácií.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">25</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">8</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="#" class="department-link">Zistiť viac →</a>
              </div>
            </div>
            
            <div class="department-card">
              <div class="card-header">
                <div class="department-icon physics">⚛️</div>
                <h3>Fyzika a materiálové vedy</h3>
              </div>
              <p class="department-description">
                Štúdium fyzikálnych javov a vývoj nových materiálov s pokročilými vlastnosťami.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">18</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">6</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="#" class="department-link">Zistiť viac →</a>
              </div>
            </div>
            
            <div class="department-card">
              <div class="card-header">
                <div class="department-icon chemistry">🧪</div>
                <h3>Chémia a farmácia</h3>
              </div>
              <p class="department-description">
                Syntéza nových zlúčenín a vývoj farmaceutických látok pre medicínu.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">22</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">10</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="#" class="department-link">Zistiť viac →</a>
              </div>
            </div>
            
            <div class="department-card">
              <div class="card-header">
                <div class="department-icon computer">💻</div>
                <h3>Informatika a AI</h3>
              </div>
              <p class="department-description">
                Vývoj algoritmov umelej inteligencie a výpočtových systémov novej generácie.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">30</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">12</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="#" class="department-link">Zistiť viac →</a>
              </div>
            </div>
            
            <div class="department-card">
              <div class="card-header">
                <div class="department-icon environment">🌍</div>
                <h3>Environmentálne vedy</h3>
              </div>
              <p class="department-description">
                Štúdium klimatických zmien a udržateľných riešení pre životné prostredie.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">16</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">7</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="#" class="department-link">Zistiť viac →</a>
              </div>
            </div>
            
            <div class="department-card">
              <div class="card-header">
                <div class="department-icon mathematics">📊</div>
                <h3>Matematika a štatistika</h3>
              </div>
              <p class="department-description">
                Teoretická matematika a aplikovaná štatistika pre vedecký výskum.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">14</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">5</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="#" class="department-link">Zistiť viac →</a>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Spolupráce a partnerstvá -->
      <section class="partnerships-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Spolupráce a partnerstvá</h2>
            <p class="section-subtitle">Naše oddelenia aktívne spolupracujú s medzinárodnými inštitúciami</p>
          </div>
          
          <div class="partnerships-grid">
            <div class="partnership-card">
              <div class="partnership-icon">🤝</div>
              <h4>Univerzitné partnerstvá</h4>
              <p>Spolupráca s 25+ univerzitami po celom svete</p>
            </div>
            <div class="partnership-card">
              <div class="partnership-icon">🏭</div>
              <h4>Priemyselné projekty</h4>
              <p>Aplikovaný výskum pre komerčnú sféru</p>
            </div>
            <div class="partnership-card">
              <div class="partnership-icon">🌐</div>
              <h4>Medzinárodné programy</h4>
              <p>Účasť v európskych výskumných programoch</p>
            </div>
            <div class="partnership-card">
              <div class="partnership-icon">💡</div>
              <h4>Inovačné centrum</h4>
              <p>Transfer technológií do praxe</p>
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
                <h3>Výskumný inštitút</h3>
                <p class="footer-tagline">Excelencia vo vede & inováciách</p>
              </div>
              <div class="contact-info">
                <div class="contact-item">
                  <span class="contact-icon">📍</span>
                  <span>Univerzitný kampus, 1180 Viedeň, Rakúsko</span>
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
              <h4>Rýchle odkazy</h4>
              <ul>
                <li><a href="/publications">Publikácie</a></li>
                <li><a href="/departments">Oddelenia</a></li>
                <li><a href="/about">O nás</a></li>
                <li><a href="/contact">Kontakt</a></li>
              </ul>
            </div>
            <div class="footer-column">
              <h4>Kontaktujte nás</h4>
              <div class="social-links">
                <a href="#" class="social-link facebook">📘 Facebook</a>
                <a href="#" class="social-link twitter">🐦 Twitter</a>
                <a href="#" class="social-link linkedin">💼 LinkedIn</a>
                <a href="#" class="social-link youtube">📺 YouTube</a>
              </div>
              <div class="newsletter">
                <h5>Newsletter</h5>
                <p>Buďte informovaní o našich najnovších výskumoch</p>
                <div class="newsletter-form">
                  <input type="email" placeholder="Váš email">
                  <button>Prihlásiť sa</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="container">
          <p>© 2025 Výskumný inštitút. Všetky práva vyhradené.</p>
          <div class="footer-links">
            <a href="/privacy">Zásady ochrany osobných údajov</a>
            <a href="/terms">Podmienky používania</a>
            <a href="/sitemap">Mapa stránok</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { articleApi, articleHelpers } from '@/services/article'
import { conferenceYearApi, conferenceYearHelpers } from '@/services/conferenceYear'

export default {
  name: 'DepartmentView',
  
  data() {
    return {
      searchOpen: false,
      quicklinksOpen: false,
      departments: [],
      loading: false
    }
  },
  
  computed: {
    // Existujúce computed properties...
  },
  
  mounted() {
    this.fetchDepartmentData()
  },
  
  methods: {
    toggleSearch() {
      this.searchOpen = !this.searchOpen
      if (this.searchOpen) this.quicklinksOpen = false
    },
    toggleQuicklinks() {
      this.quicklinksOpen = !this.quicklinksOpen
      if (this.quicklinksOpen) this.searchOpen = false
    },
    fetchDepartmentData() {
      // Existujúce metódy...
    }
  }
}
</script>

<style scoped>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

/* CSS Variables - rovnaké ako v HomeView */
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

/* Všetky base styles z HomeView */
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

/* Horná lišta - rovnaké ako HomeView */
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

/* Search & Quicklinks Dropdowns - rovnaké ako HomeView */
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

/* Hlavné menu - rovnaké ako HomeView */
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

.main-nav-link:hover,
.main-nav-link.active {
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

.main-nav-link:hover .nav-underline,
.main-nav-link.active .nav-underline {
  width: 100%;
}

/* Hero sekcia */
.hero-section {
  position: relative;
  min-height: 60vh;
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
  color: white;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 2rem;
}

.hero-title {
  font-family: 'Poppins', sans-serif;
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  line-height: 1.2;
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
  margin-bottom: 3rem;
  opacity: 0.9;
  line-height: 1.6;
}

.hero-stats {
  display: flex;
  justify-content: center;
  gap: 4rem;
  margin-top: 2rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  display: block;
  font-size: 2.5rem;
  font-weight: 700;
  font-family: 'Poppins', sans-serif;
  color: #ffd700;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 1rem;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Section Headers */
.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-title {
  font-family: 'Poppins', sans-serif;
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
  line-height: 1.6;
}

/* Departments Section */
.departments-section {
  padding: 5rem 0;
  background: var(--bg-primary);
}

.departments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
  margin-top: 3rem;
}

.department-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.department-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--gradient-primary);
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 1;
}

.department-card:hover::before {
  opacity: 0.05;
}

.department-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 2;
}

.department-icon {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  flex-shrink: 0;
}

.department-icon.biology { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.department-icon.physics { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.department-icon.chemistry { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.department-icon.computer { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.department-icon.environment { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.department-icon.mathematics { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }

.department-card h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.3;
}

.department-description {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 2;
}

.department-stats {
  display: flex;
  gap: 2rem;
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 2;
}

.stat {
  text-align: center;
}

.stat .stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-bottom: 0.25rem;
}

.stat .stat-label {
  font-size: 0.875rem;
  color: var(--text-light);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.card-footer {
  position: relative;
  z-index: 2;
}

.department-link {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.department-link:hover {
  color: var(--primary-dark);
  transform: translateX(5px);
}

/* Partnerships Section */
.partnerships-section {
  padding: 5rem 0;
  background: var(--bg-secondary);
}

.partnerships-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-top: 3rem;
}

.partnership-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  text-align: center;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.partnership-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

.partnership-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.partnership-card h4 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.partnership-card p {
  color: var(--text-secondary);
  line-height: 1.6;
}

/* Footer - rovnaké ako HomeView */
.footer {
  background: var(--bg-dark);
  color: white;
  margin-top: auto;
}

.footer-content {
  padding: 3rem 0;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 3rem;
}

.footer-column.main {
  padding-right: 2rem;
}

.footer-logo h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  margin-bottom: 0.5rem;
}

.footer-tagline {
  color: #9ca3af;
  margin-bottom: 2rem;
  font-size: 0.9rem;
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
  color: #d1d5db;
  font-size: 0.9rem;
}

.contact-icon {
  font-size: 1rem;
}

.footer-column h4 {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: white;
}

.footer-column ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-column li {
  margin-bottom: 0.5rem;
}

.footer-column a {
  color: #9ca3af;
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 0.9rem;
}

.footer-column a:hover {
  color: white;
}

.social-links {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.social-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #9ca3af;
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 0.9rem;
}

.social-link:hover {
  color: white;
}

.newsletter h5 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: white;
}

.newsletter p {
  color: #9ca3af;
  font-size: 0.85rem;
  margin-bottom: 1rem;
}

.newsletter-form {
  display: flex;
  gap: 0.5rem;
}

.newsletter-form input {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #374151;
  border-radius: 8px;
  background: #374151;
  color: white;
  font-size: 0.85rem;
}

.newsletter-form input::placeholder {
  color: #9ca3af;
}

.newsletter-form button {
  padding: 0.5rem 1rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.85rem;
  transition: background 0.3s ease;
}

.newsletter-form button:hover {
  background: var(--primary-dark);
}

.footer-bottom {
  background: #111827;
  padding: 1.5rem 0;
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
  color: #9ca3af;
  font-size: 0.85rem;
}

.footer-links {
  display: flex;
  gap: 1.5rem;
}

.footer-links a {
  color: #9ca3af;
  text-decoration: none;
  font-size: 0.85rem;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
  .topbar-container {
    padding: 0.5rem 1rem;
  }
  
  .navbar-container {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }
  
  .main-nav-links {
    gap: 1rem;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 2rem;
  }
  
  .departments-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .partnerships-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }
  
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .footer-bottom .container {
    flex-direction: column;
    text-align: center;
  }
  
  .quicklinks-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .departments-grid {
    gap: 1rem;
  }
  
  .department-card {
    padding: 1.5rem;
  }
  
  .partnerships-grid {
    grid-template-columns: 1fr;
  }
}
</style>