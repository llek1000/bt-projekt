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
          <a href="/about" class="main-nav-link active">
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
              <span class="title-line">O našom</span>
              <span class="title-line highlight">Inštitúte</span>
            </h1>
            <p class="hero-subtitle">
              Spoznajte našu históriu, víziu a tím výskumníkov, ktorí posúvajú hranice vedy
            </p>
          </div>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">25+</span>
              <span class="stat-label">Rokov skúseností</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">150+</span>
              <span class="stat-label">Výskumníkov</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">40+</span>
              <span class="stat-label">Krajín spolupráce</span>
            </div>
          </div>
        </div>
      </section>
      
      <!-- História a vízia -->
      <section class="history-vision-section">
        <div class="container">
          <div class="content-grid">
            <div class="content-card">
              <div class="card-icon">📚</div>
              <h2>Naša história</h2>
              <p>
                Výskumný inštitút bol založený v roku 1999 s víziou stať sa vedúcim centrom pre 
                vedecký výskum v strednej Európe. Začali sme ako malá skupina nadšených vedcov 
                a postupne sme sa rozrástli na medzinárodne uznávanú inštitúciu.
              </p>
              <p>
                Počas uplynulých 25 rokov sme publikovali viac ako 2000 vedeckých prác, 
                získali množstvo prestížnych ocenení a vychovali stovky mladých výskumníkov, 
                ktorí dnes pôsobia po celom svete.
              </p>
            </div>
            
            <div class="content-card">
              <div class="card-icon">🎯</div>
              <h2>Naša vízia</h2>
              <p>
                Naším cieľom je byť lídrom v inovatívnom výskume, ktorý prispieva k riešeniu 
                globálnych výziev súčasnosti. Zameriavame sa na interdisciplinárny prístup, 
                ktorý spája rôzne vedecké oblasti.
              </p>
              <p>
                Veríme, že kvalitný výskum musí byť dostupný a užitočný pre spoločnosť. 
                Preto kladieme dôraz na transfer poznatkov do praxe a spoluprácu s priemyslom 
                a verejným sektorom.
              </p>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Hodnoty a princípy -->
      <section class="values-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Naše hodnoty</h2>
            <p class="section-subtitle">Princípy, ktorými sa riadime v každodennej práci</p>
          </div>
          
          <div class="values-grid">
            <div class="value-card">
              <div class="value-icon">🔬</div>
              <h3>Vedecká excelencia</h3>
              <p>Usilujeme sa o najvyššiu kvalitu vo všetkých našich výskumných aktivitách a publikáciách.</p>
            </div>
            
            <div class="value-card">
              <div class="value-icon">🤝</div>
              <h3>Spolupráca</h3>
              <p>Podporujeme otvorenosť, zdieľanie poznatkov a medzinárodnú spoluprácu.</p>
            </div>
            
            <div class="value-card">
              <div class="value-icon">💡</div>
              <h3>Inovácia</h3>
              <p>Hľadáme nové prístupy a riešenia pre komplexné vedecké a spoločenské problémy.</p>
            </div>
            
            <div class="value-card">
              <div class="value-icon">🌱</div>
              <h3>Udržateľnosť</h3>
              <p>Zodpovedne pristupujeme k životnému prostrediu a podporujeme udržateľný rozvoj.</p>
            </div>
            
            <div class="value-card">
              <div class="value-icon">🎓</div>
              <h3>Vzdelávanie</h3>
              <p>Investujeme do rozvoja mladých vedcov a popularizácie vedy.</p>
            </div>
            
            <div class="value-card">
              <div class="value-icon">⚡</div>
              <h3>Dopad na spoločnosť</h3>
              <p>Naša práca má prinášať konkrétne prínosy pre ľudstvo a planétu.</p>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Vedenie inštitútu -->
      <section class="leadership-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Vedenie inštitútu</h2>
            <p class="section-subtitle">Skúsení lídri, ktorí vedú náš inštitút k novým výšinám</p>
          </div>
          
          <div class="leadership-grid">
            <div class="leader-card">
              <div class="leader-image">
                <div class="leader-avatar">👨‍🔬</div>
              </div>
              <div class="leader-info">
                <h3>Prof. Dr. Ing. Martin Novák</h3>
                <p class="leader-title">Riaditeľ inštitútu</p>
                <p class="leader-description">
                  Špecialista na kvantovú fyziku s 30-ročnou praxou. Autor viac ako 200 publikácií 
                  v prestížnych časopisoch.
                </p>
                <div class="leader-credentials">
                  <span class="credential">PhD. Cambridge University</span>
                  <span class="credential">Fellow Royal Society</span>
                </div>
              </div>
            </div>
            
            <div class="leader-card">
              <div class="leader-image">
                <div class="leader-avatar">👩‍🔬</div>
              </div>
              <div class="leader-info">
                <h3>Prof. Dr. Anna Svoboda</h3>
                <p class="leader-title">Vedecká riaditeľka</p>
                <p class="leader-description">
                  Expertka na biotechnológie a molekulárnu biológiu. Nositeľka európskej ceny 
                  za výskum v oblasti medicíny.
                </p>
                <div class="leader-credentials">
                  <span class="credential">PhD. Max Planck Institute</span>
                  <span class="credential">ERC Advanced Grant</span>
                </div>
              </div>
            </div>
            
            <div class="leader-card">
              <div class="leader-image">
                <div class="leader-avatar">👨‍💼</div>
              </div>
              <div class="leader-info">
                <h3>Ing. Peter Kovář, MBA</h3>
                <p class="leader-title">Prevádzkový riaditeľ</p>
                <p class="leader-description">
                  Skúsený manažér s 20-ročnou praxou v riadení výskumných projektov 
                  a technologickom transfere.
                </p>
                <div class="leader-credentials">
                  <span class="credential">MBA Stanford</span>
                  <span class="credential">PMP Certified</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Ocenenia a úspechy -->
      <section class="achievements-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Naše úspechy</h2>
            <p class="section-subtitle">Ocenenia a míľniky, ktorých sme dosiahli</p>
          </div>
          
          <div class="achievements-timeline">
            <div class="timeline-item">
              <div class="timeline-year">2023</div>
              <div class="timeline-content">
                <h4>Európska cena za excelentnosť vo výskume</h4>
                <p>Prvé miesto v kategórii interdisciplinárny výskum za projekt AI v medicíne</p>
              </div>
            </div>
            
            <div class="timeline-item">
              <div class="timeline-year">2022</div>
              <div class="timeline-content">
                <h4>Patent roka</h4>
                <p>Registrácia revolučnej technológie pre čistenie odpadových vôd</p>
              </div>
            </div>
            
            <div class="timeline-item">
              <div class="timeline-year">2021</div>
              <div class="timeline-content">
                <h4>Top 10 výskumných inštitútov</h4>
                <p>Zaradenie medzi 10 najlepších výskumných centier v strednej Európe</p>
              </div>
            </div>
            
            <div class="timeline-item">
              <div class="timeline-year">2020</div>
              <div class="timeline-content">
                <h4>Cena za inováciu</h4>
                <p>Národná cena za vývoj nových materiálov pre obnoviteľné zdroje energie</p>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Kontakt a lokácia -->
      <section class="contact-location-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Navštívte nás</h2>
            <p class="section-subtitle">Kde nás nájdete a ako nás kontaktovať</p>
          </div>
          
          <div class="contact-grid">
            <div class="contact-info">
              <h3>Kontaktné informácie</h3>
              <div class="contact-details">
                <div class="contact-item">
                  <span class="contact-icon">📍</span>
                  <div class="contact-text">
                    <strong>Adresa:</strong>
                    <p>Univerzitný kampus 1<br>1180 Viedeň, Rakúsko</p>
                  </div>
                </div>
                
                <div class="contact-item">
                  <span class="contact-icon">📞</span>
                  <div class="contact-text">
                    <strong>Telefón:</strong>
                    <p>+43 1 47654 0</p>
                  </div>
                </div>
                
                <div class="contact-item">
                  <span class="contact-icon">✉️</span>
                  <div class="contact-text">
                    <strong>Email:</strong>
                    <p>info@research-institute.ac.at</p>
                  </div>
                </div>
                
                <div class="contact-item">
                  <span class="contact-icon">🌐</span>
                  <div class="contact-text">
                    <strong>Web:</strong>
                    <p>www.research-institute.ac.at</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="visit-info">
              <h3>Návštevné hodiny</h3>
              <div class="visit-schedule">
                <div class="schedule-item">
                  <span class="day">Pondelok - Piatok:</span>
                  <span class="time">8:00 - 18:00</span>
                </div>
                <div class="schedule-item">
                  <span class="day">Sobota:</span>
                  <span class="time">9:00 - 14:00</span>
                </div>
                <div class="schedule-item">
                  <span class="day">Nedeľa:</span>
                  <span class="time">Zatvorené</span>
                </div>
              </div>
              
              <div class="visit-note">
                <p><strong>Poznámka:</strong> Pre návštevu laboratórií je potrebná predchádzajúca registrácia.</p>
              </div>
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
export default {
  name: 'AboutView',
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

/* História a vízia section */
.history-vision-section {
  padding: 5rem 0;
  background: var(--bg-primary);
}

.content-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 3rem;
  margin-top: 2rem;
}

.content-card {
  background: white;
  border-radius: 20px;
  padding: 3rem;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.content-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.card-icon {
  font-size: 3rem;
  margin-bottom: 1.5rem;
}

.content-card h2 {
  font-family: 'Poppins', sans-serif;
  font-size: 2rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
}

.content-card p {
  color: var(--text-secondary);
  line-height: 1.8;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

/* Hodnoty section */
.values-section {
  padding: 5rem 0;
  background: var(--bg-secondary);
}

.values-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 3rem;
}

.value-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  text-align: center;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.value-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
}

.value-icon {
  font-size: 3rem;
  margin-bottom: 1.5rem;
}

.value-card h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.value-card p {
  color: var(--text-secondary);
  line-height: 1.6;
}

/* Vedenie section */
.leadership-section {
  padding: 5rem 0;
  background: var(--bg-primary);
}

.leadership-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
  margin-top: 3rem;
}

.leader-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  text-align: center;
}

.leader-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.leader-image {
  margin-bottom: 1.5rem;
}

.leader-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: var(--gradient-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  margin: 0 auto;
  margin-bottom: 1rem;
}

.leader-info h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.leader-title {
  color: var(--primary-color);
  font-weight: 600;
  margin-bottom: 1rem;
}

.leader-description {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.leader-credentials {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  justify-content: center;
}

.credential {
  background: var(--bg-secondary);
  color: var(--text-primary);
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Úspechy section */
.achievements-section {
  padding: 5rem 0;
  background: var(--bg-secondary);
}

.achievements-timeline {
  position: relative;
  max-width: 800px;
  margin: 3rem auto 0;
}

.achievements-timeline::before {
  content: '';
  position: absolute;
  left: 50%;
  top: 0;
  bottom: 0;
  width: 2px;
  background: var(--primary-color);
  transform: translateX(-50%);
}

.timeline-item {
  position: relative;
  margin-bottom: 3rem;
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  gap: 2rem;
  align-items: center;
}

.timeline-item:nth-child(even) {
  direction: rtl;
}

.timeline-item:nth-child(even) .timeline-content {
  direction: ltr;
  text-align: right;
}

.timeline-year {
  background: var(--primary-color);
  color: white;
  padding: 1rem;
  border-radius: 50%;
  font-weight: 700;
  font-family: 'Poppins', sans-serif;
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 2;
}

.timeline-content {
  background: white;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
}

.timeline-content h4 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.timeline-content p {
  color: var(--text-secondary);
  line-height: 1.6;
}

/* Kontakt section */
.contact-location-section {
  padding: 5rem 0;
  background: var(--bg-primary);
}

.contact-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 3rem;
  margin-top: 3rem;
}

.contact-info,
.visit-info {
  background: white;
  border-radius: 20px;
  padding: 3rem;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
}

.contact-info h3,
.visit-info h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 2rem;
}

.contact-details {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.contact-icon {
  font-size: 1.5rem;
  min-width: 2rem;
}

.contact-text strong {
  color: var(--text-primary);
  display: block;
  margin-bottom: 0.25rem;
}

.contact-text p {
  color: var(--text-secondary);
  margin: 0;
  line-height: 1.6;
}

.visit-schedule {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 2rem;
}

.schedule-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: var(--bg-secondary);
  border-radius: 8px;
}

.day {
  font-weight: 500;
  color: var(--text-primary);
}

.time {
  color: var(--text-secondary);
}

.visit-note {
  padding: 1rem;
  background: #fef3cd;
  border: 1px solid #fdd835;
  border-radius: 8px;
}

.visit-note p {
  margin: 0;
  color: #8a6d00;
  font-size: 0.9rem;
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

.footer .contact-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.footer .contact-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #d1d5db;
  font-size: 0.9rem;
}

.footer .contact-icon {
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
  
  .content-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .values-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }
  
  .leadership-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .achievements-timeline::before {
    left: 30px;
  }
  
  .timeline-item {
    grid-template-columns: auto 1fr;
    gap: 1rem;
    margin-left: 60px;
  }
  
  .timeline-item:nth-child(even) {
    direction: ltr;
  }
  
  .timeline-item:nth-child(even) .timeline-content {
    text-align: left;
  }
  
  .timeline-year {
    position: absolute;
    left: -90px;
  }
  
  .contact-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
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
  
  .content-card {
    padding: 2rem;
  }
  
  .values-grid {
    grid-template-columns: 1fr;
  }
  
  .contact-info,
  .visit-info {
    padding: 2rem;
  }
}
</style>
