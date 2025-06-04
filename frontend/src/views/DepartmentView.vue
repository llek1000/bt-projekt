<template>
  <div class="page-container">
    <!-- Použitie spoločného navbar komponentu -->
    <NavbarComponent />
    
    <!-- Hlavný obsah -->
    <main class="main-content">
      <!-- Hero sekcia -->
      <section class="hero-section">
        <div class="hero-background">
          <div class="hero-overlay"></div>
          <div class="hero-particles"></div>
        </div>
        <div class="hero-content">
          <div class="container">
            <h1 class="hero-title">
              <span class="title-line">Naše</span>
              <span class="title-line highlight">Oddelenia</span>
            </h1>
            <p class="hero-subtitle">
              Preskúmajte naše špecializované výskumné oddelenia a ich inovatívne projekty
            </p>
            <div class="hero-stats">
              <div class="stat-item">
                <span class="stat-number">6</span>
                <span class="stat-label">Oddelení</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">150+</span>
                <span class="stat-label">Výskumníkov</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">50+</span>
                <span class="stat-label">Aktívnych projektov</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Departments Section -->
      <section class="departments-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Výskumné oddelenia</h2>
            <p class="section-subtitle">Každé oddelenie sa špecializuje na špecifické oblasti vedy a výskumu</p>
          </div>
          
          <div class="departments-grid">
            <div class="department-card">
              <div class="card-header">
                <div class="department-icon biology">🧬</div>
                <h3>Biológia a biotechnológie</h3>
              </div>
              <p class="department-description">
                Naše biologické oddelenie sa zaoberá výskumom živých organizmov, 
                génových terapií a biotechnologických aplikácií pre medicínu a poľnohospodárstvo.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">25</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">12</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="/departments/biology" class="department-link">Zistiť viac →</a>
              </div>
            </div>

            <div class="department-card">
              <div class="card-header">
                <div class="department-icon physics">⚛️</div>
                <h3>Fyzika a materiálové vedy</h3>
              </div>
              <p class="department-description">
                Výskum pokročilých materiálov, kvantovej fyziky a nanotechnológií 
                s aplikáciami v energetike a elektronike.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">30</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">8</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="/departments/physics" class="department-link">Zistiť viac →</a>
              </div>
            </div>

            <div class="department-card">
              <div class="card-header">
                <div class="department-icon chemistry">🧪</div>
                <h3>Chémia a chemické inžinierstvo</h3>
              </div>
              <p class="department-description">
                Vývoj nových chemických zlúčenín, katalyzátorov a procesov 
                pre farmaceutický a chemický priemysel.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">20</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">10</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="/departments/chemistry" class="department-link">Zistiť viac →</a>
              </div>
            </div>

            <div class="department-card">
              <div class="card-header">
                <div class="department-icon computer">💻</div>
                <h3>Informatika a umelá inteligencia</h3>
              </div>
              <p class="department-description">
                Pokročilý výskum v oblasti strojového učenia, umelej inteligencie 
                a výpočtových metód pre vedecké aplikácie.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">35</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">15</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="/departments/computer-science" class="department-link">Zistiť viac →</a>
              </div>
            </div>

            <div class="department-card">
              <div class="card-header">
                <div class="department-icon environment">🌍</div>
                <h3>Environmentálne vedy</h3>
              </div>
              <p class="department-description">
                Štúdium klimatických zmien, biodiverzity a vývoj udržateľných 
                riešení pre ochranu životného prostredia.
              </p>
              <div class="department-stats">
                <div class="stat">
                  <span class="stat-number">28</span>
                  <span class="stat-label">Výskumníkov</span>
                </div>
                <div class="stat">
                  <span class="stat-number">9</span>
                  <span class="stat-label">Projektov</span>
                </div>
              </div>
              <div class="card-footer">
                <a href="/departments/environment" class="department-link">Zistiť viac →</a>
              </div>
            </div>

            <div class="department-card">
              <div class="card-header">
                <div class="department-icon mathematics">📊</div>
                <h3>Matematika a štatistika</h3>
              </div>
              <p class="department-description">
                Teoretický a aplikovaný matematický výskum, štatistické modelovanie 
                a dátová analýza pre podporu ostatných oddelení.
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
                <a href="/departments/mathematics" class="department-link">Zistiť viac →</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Partnerships Section -->
      <section class="partnerships-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Naše partnerstvá</h2>
            <p class="section-subtitle">Spolupracujeme s popredými inštitúciami na celom svete</p>
          </div>
          
          <div class="partnerships-grid">
            <div class="partnership-card">
              <div class="partnership-icon">🏛️</div>
              <h4>Univerzitné partnerstvá</h4>
              <p>Spolupráca s Harvard, MIT, Oxford a ďalšími prestížnymi univerzitami</p>
            </div>
            
            <div class="partnership-card">
              <div class="partnership-icon">🏢</div>
              <h4>Priemyselné partnerstvá</h4>
              <p>Projekty s vedúcimi technologickými spoločnosťami a farmaceutickými firmami</p>
            </div>
            
            <div class="partnership-card">
              <div class="partnership-icon">🌐</div>
              <h4>Medzinárodné organizácie</h4>
              <p>Členstvo v európskych a svetových vedeckých konzorciách</p>
            </div>
            
            <div class="partnership-card">
              <div class="partnership-icon">🏛️</div>
              <h4>Vládne inštitúcie</h4>
              <p>Spolupráca s ministerstvami a národnými vedeckými agentúrami</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <FooterComponent />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

export default defineComponent({
  name: 'DepartmentView',
  components: {
    NavbarComponent,
    FooterComponent
  }
})
</script>

<style scoped>
/* CSS Variables */
:root {
  --primary-color: #2563eb;
  --primary-dark: #1d4ed8;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --border-color: #e5e7eb;
  --light-bg: #f9fafb;
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Base Styles */
* {
  margin: 0;
  padding: 0;
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
  padding: 0 1rem;
}

.main-content {
  flex: 1;
}

/* Hero sekcia */
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 6rem 0 4rem;
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
  width: 100%;
  height: 100%;
  overflow: hidden;
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

/* Section Headers */
.section-header {
  text-align: center;
  margin-bottom: 4rem;
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

/* Departments Section */
.departments-section {
  padding: 6rem 0;
  background: white;
}

.departments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
}

.department-card {
  background: var(--light-bg);
  border-radius: 12px;
  padding: 2rem;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.department-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.department-card:hover::before {
  transform: scaleX(1);
}

.department-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.department-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.department-icon.biology { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.department-icon.physics { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.department-icon.chemistry { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.department-icon.computer { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.department-icon.environment { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.department-icon.mathematics { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }

.department-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
}

.department-description {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.department-stats {
  display: flex;
  gap: 2rem;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: white;
  border-radius: 8px;
}

.stat {
  text-align: center;
}

.stat .stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
}

.stat .stat-label {
  display: block;
  font-size: 0.75rem;
  color: var(--text-secondary);
  margin-top: 0.25rem;
}

.card-footer {
  text-align: center;
}

.department-link {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.department-link:hover {
  color: var(--primary-dark);
}

/* Partnerships Section */
.partnerships-section {
  padding: 6rem 0;
  background: var(--light-bg);
}

.partnerships-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.partnership-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.partnership-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
}

.partnership-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.partnership-card h4 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.partnership-card p {
  color: var(--text-secondary);
  line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 2rem;
  }
  
  .departments-grid {
    grid-template-columns: 1fr;
  }
  
  .department-stats {
    justify-content: center;
  }
  
  .partnerships-grid {
    grid-template-columns: 1fr;
  }
}
</style>