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
                <li><a href="#">Knižnica</a></li>
                <li><a href="#">Online výučba</a></li>
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
          <a href="/departments" class="main-nav-link">
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
              <span class="title-line">Centrum pre</span>
              <span class="title-line highlight">Vedecký výskum</span>
            </h1>
            <p class="hero-subtitle">
              Posúvanie hraníc poznania prostredníctvom inovácií a spolupráce
            </p>
            <div class="hero-buttons">
              <a href="/about" class="hero-btn secondary">
                <span>O nás</span>
                <span class="btn-arrow">→</span>
              </a>
            </div>
          </div>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">150+</span>
              <span class="stat-label">Projektov</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">200+</span>
              <span class="stat-label">Publikácií</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">500+</span>
              <span class="stat-label">Partnerov</span>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Vítacia sekcia -->
      <section class="welcome-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Vitajte v budúcnosti výskumu</h2>
            <p class="section-subtitle">Kde sa inovácie stretávajú s excelentnosťou</p>
          </div>
          <div class="welcome-content">
            <div class="welcome-text">
              <p class="highlight-text">
                Náš inštitút je zameraný na pokrok vo vedeckom poznaní prostredníctvom inovatívneho výskumu v rôznych disciplínach.
              </p>
              <p>
                Spojujeme svetových odborníkov, moderné zariadenia a inovatívne prístupy na riešenie najnaliehavejších výziev našej doby.
              </p>
              <p>
                Prostredníctvom spolupráce s priemyslom, vládou a inými akademickými inštitúciami sa snažíme previesť vedecké objavy 
                do praktických aplikácií, ktoré prospievajú spoločnosti a zlepšujú životy.
              </p>
            </div>
            <div class="welcome-features">
              <div class="feature-item">
                <div class="feature-icon">🎯</div>
                <h4>Zameraný výskum</h4>
                <p>Cielené štúdie v kľúčových vedeckých oblastiach</p>
              </div>
              <div class="feature-item">
                <div class="feature-icon">🤝</div>
                <h4>Spolupráca</h4>
                <p>Globálne partnerstvá a zdieľanie poznatkov</p>
              </div>
              <div class="feature-item">
                <div class="feature-icon">💡</div>
                <h4>Inovácia</h4>
                <p>Moderné technológie a metódy</p>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Sekcia výskumných oblastí -->
      <section class="research-areas">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Výskumné oblasti</h2>
            <p class="section-subtitle">Preskúmajte naše kľúčové oblasti pôsobenia</p>
          </div>
          <div class="research-grid">
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon life-sciences">🧪</div>
                <h3>Životné vedy</h3>
              </div>
              <p>Skúmanie základných biologických procesov a vývoj nových prístupov k zdraviu a chorobám.</p>
              <div class="card-footer">
                <a href="#" class="learn-more">Zistiť viac →</a>
              </div>
            </div>
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon data-science">💻</div>
                <h3>Údaje a vedy o údajoch</h3>
              </div>
              <p>Vývoj výpočtových metód a algoritmov AI na extrakciu poznatkov z komplexných dátových súborov.</p>
              <div class="card-footer">
                <a href="#" class="learn-more">Zistiť viac →</a>
              </div>
            </div>
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon environmental">🌍</div>
                <h3>Environmentálne vedy</h3>
              </div>
              <p>Štúdium zmien klímy, biodiverzity a vývoj udržateľných riešení pre environmentálne výzvy.</p>
              <div class="card-footer">
                <a href="#" class="learn-more">Zistiť viac →</a>
              </div>
            </div>
            <div class="research-card">
              <div class="card-header">
                <div class="research-icon materials">⚡</div>
                <h3>Vedy o materiáloch</h3>
              </div>
              <p>Tvorba a analýza nových materiálov s aplikáciami v energetike, zdravotnej starostlivosti a technológii.</p>
              <div class="card-footer">
                <a href="#" class="learn-more">Zistiť viac →</a>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Najnovšie správy -->
      <section class="latest-news">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Najnovšie správy</h2>
            <p class="section-subtitle">Aktuality z nášho inštitútu</p>
          </div>
          <div class="news-grid">
            <article class="news-card featured">
              <div class="news-image">
                <div class="news-category">Prelom</div>
              </div>
              <div class="news-content">
                <div class="news-meta">
                  <span class="news-date">18. mája 2025</span>
                  <span class="reading-time">5 min čítania</span>
                </div>
                <h3>Veľký prielom v kvantovej výpočtovej technike</h3>
                <p>Naši výskumníci dosiahli významný míľnik v kvantovej korekcii chýb, čo priblížilo praktické kvantové počítanie k realite.</p>
                <a href="#" class="read-more">Prečítať celú správu →</a>
              </div>
            </article>
            <article class="news-card">
              <div class="news-content">
                <div class="news-meta">
                  <span class="news-date">5. mája 2025</span>
                  <span class="reading-time">3 min čítania</span>
                </div>
                <h3>Nová medzinárodná výskumná spolupráca</h3>
                <p>Inštitút nadviazal nové partnerstvo s poprednými výskumnými centrami v Európe a Ázii.</p>
                <a href="#" class="read-more">Prečítať viac →</a>
              </div>
            </article>
            <article class="news-card">
              <div class="news-content">
                <div class="news-meta">
                  <span class="news-date">22. apríla 2025</span>
                  <span class="reading-time">2 min čítania</span>
                </div>
                <h3>Oznámenie o ročnej vedeckej sympózii</h3>
                <p>15. ročník Vedeckej sympózy sa uskutoční tento október, s hlavnými rečníkmi z celého sveta.</p>
                <a href="#" class="read-more">Prečítať viac →</a>
              </div>
            </article>
          </div>
        </div>
      </section>
      
      <!-- Udalosti a zaujímavosti oddelení -->
      <section class="events-departments">
        <div class="container">
          <div class="section-grid">
            <!-- Nadchádzajúce udalosti -->
            <div class="events-section">
              <h2 class="section-title">Nadchádzajúce udalosti</h2>
              <div class="events-list">
                <div class="event-card">
                  <div class="event-date">
                    <span class="date-number">12</span>
                    <span class="date-month">JUN</span>
                  </div>
                  <div class="event-details">
                    <h4>Workshop o výskumnej metodológii</h4>
                    <p>Pokročilé výskumné metodológie a techniky analýzy dát.</p>
                    <div class="event-meta">
                      <span class="event-time">⏰ 10:00 - 16:00</span>
                      <span class="event-location">📍 Budova vedy</span>
                    </div>
                  </div>
                </div>
                <div class="event-card">
                  <div class="event-date">
                    <span class="date-number">24</span>
                    <span class="date-month">JUL</span>
                  </div>
                  <div class="event-details">
                    <h4>Budúcnosť obnoviteľnej energie</h4>
                    <p>Prednáška hostí Dr. Eleny Rodriguezovej z MIT.</p>
                    <div class="event-meta">
                      <span class="event-time">⏰ 14:00 - 16:00</span>
                      <span class="event-location">📍 Hlavná audítka</span>
                    </div>
                  </div>
                </div>
              </div>
              <a href="/events" class="view-all-btn">Zobraziť všetky udalosti →</a>
            </div>
            
            <!-- Zaujímavosti oddelení -->
            <div class="departments-section">
              <h2 class="section-title">Naše oddelenia</h2>
              <div class="departments-grid">
                <a href="/departments/animal-science" class="department-card">
                  <div class="department-icon">🐄</div>
                  <h4>Živočíšna veda</h4>
                  <p>Udržateľné poľnohospodárske praktiky</p>
                </a>
                <a href="/departments/biochemistry" class="department-card">
                  <div class="department-icon">🧬</div>
                  <h4>Biochémia</h4>
                  <p>Chemické procesy v živote</p>
                </a>
                <a href="/departments/physics" class="department-card">
                  <div class="department-icon">⚛️</div>
                  <h4>Fyzika</h4>
                  <p>Základné princípy</p>
                </a>
                <a href="/departments/earth-sciences" class="department-card">
                  <div class="department-icon">🌋</div>
                  <h4>Vedy o Zemi</h4>
                  <p>Charakteristiky planéty</p>
                </a>
              </div>
              <a href="/departments" class="view-all-btn">Zobraziť všetky oddelenia →</a>
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
                <li><a href="/research">Výskum</a></li>
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

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'HomeView',
  data() {
    return {
      searchOpen: false,
      quicklinksOpen: false
    }
  },
  methods: {
    toggleSearch() {
      this.searchOpen = !this.searchOpen
      if (this.searchOpen) this.quicklinksOpen = false
    },
    toggleQuicklinks() {
      this.quicklinksOpen = !this.quicklinksOpen
      if (this.quicklinksOpen) this.searchOpen = false
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

/* Horná lišta */
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

/* Hlavné menu */
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

/* Hero sekcia */
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

/* Vítacia sekcia */
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

/* Výskumné oblasti */
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

/* Najnovšie správy */
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
  color: black;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
  backdrop-filter: blur(10px);
  font-size: 50px;
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

/* Udalosti a oddelenia */
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
  color: black;
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
  color: black;
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
  color: black;
  font-size: 0.875rem;
}

.contact-icon {
  font-size: 1rem;
}

.footer-column h4 {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: black;
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
  color: black;
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 0.875rem;
}

.footer-column a:hover {
  color: gray;
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
  color: black;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 0.875rem;
}

.social-link:hover {
  color: gray;
  transform: translateX(5px);
}

.newsletter h5 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: black;
}

.newsletter p {
  color: black;
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
  background: #445a79;
  color: white;
  font-size: 0.875rem;
  outline: none;
}

.newsletter-form input::placeholder {
  color:white;
}

.newsletter-form button {
  padding: 0.75rem 1rem;
  background: var(--primary-color);
  color: white;
  border: none;
  background: green;
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
  background: #445a79;
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
  color: white;
  font-size: 0.875rem;
}

.footer-links {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.footer-links a {
  color: white;
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: gray;
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