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
              <span class="title-line">O nás</span>
              <span class="title-line highlight">Naša história a vízia</span>
            </h1>
            <p class="hero-subtitle">
              Spoznajte náš príbeh, hodnoty a ľudí, ktorí formujú budúcnosť vedeckého výskumu
            </p>
            <div class="hero-stats">
              <div class="stat-item">
                <span class="stat-number">25+</span>
                <span class="stat-label">Rokov skúseností</span>
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
        </div>
      </section>

      <!-- História a vízia section -->
      <section class="history-vision-section">
        <div class="container">
          <div class="content-grid">
            <div class="content-card">
              <div class="card-icon">📚</div>
              <h2>Naša história</h2>
              <p>
                Založený v roku 1998, náš inštitút vznikol z vízie vytvoriť centrum excelentnosti 
                vo vedeckom výskume. Za viac ako 25 rokov sme sa stali vedúcou inštitúciou 
                v oblasti biotechnológií, environmentálnych vied a dátovej analýzy.
              </p>
              <p>
                Naša cesta začala s malým tímom nadšených výskumníkov a dnes združujeme 
                viac ako 150 odborníkov z celého sveta, ktorí spoločne pracujú na riešení 
                najnaliehavejších výziev našej doby.
              </p>
            </div>
            
            <div class="content-card">
              <div class="card-icon">🎯</div>
              <h2>Naša vízia</h2>
              <p>
                Stať sa globálne uznávaným centrom inovácií, kde sa veda stretáva s praxou 
                a kde každý objav prispieva k lepšej budúcnosti pre ľudstvo.
              </p>
              <p>
                Veríme, že prostredníctvom interdisciplinárnej spolupráce, najmodernejších 
                technológií a neustálej snahy o excelentnosť môžeme prekonať hranice 
                súčasného poznania a vytvoriť riešenia, ktoré zmenia svet k lepšiemu.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Hodnoty section -->
      <section class="values-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Naše hodnoty</h2>
            <p class="section-subtitle">Princípy, ktoré nás vedú v každom kroku</p>
          </div>
          <div class="values-grid">
            <div class="value-card">
              <div class="value-icon">🔬</div>
              <h3>Vedecká excelentnosť</h3>
              <p>Neustále sa snažíme o najvyššiu kvalitu vo všetkých našich výskumných aktivitách</p>
            </div>
            <div class="value-card">
              <div class="value-icon">🤝</div>
              <h3>Spolupráca</h3>
              <p>Veríme v silu tímovej práce a medzinárodnej spolupráce</p>
            </div>
            <div class="value-card">
              <div class="value-icon">💡</div>
              <h3>Inovácia</h3>
              <p>Podporujeme kreatívne myslenie a inovatívne prístupy k riešeniu problémov</p>
            </div>
            <div class="value-card">
              <div class="value-icon">🌱</div>
              <h3>Udržateľnosť</h3>
              <p>Zaväzujeme sa k environmentálne zodpovednému výskumu a praxi</p>
            </div>
            <div class="value-card">
              <div class="value-icon">📖</div>
              <h3>Vzdelávanie</h3>
              <p>Podporujeme rozvoj mladých talentov a šírenie vedeckých poznatkov</p>
            </div>
            <div class="value-card">
              <div class="value-icon">🌍</div>
              <h3>Globálny dosah</h3>
              <p>Naša práca má pozitívny vplyv na komunity po celom svete</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Vedenie section -->
      <section class="leadership-section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">Naše vedenie</h2>
            <p class="section-subtitle">Stretnutie s ľuďmi, ktorí vedú náš inštitút</p>
          </div>
          <div class="leadership-grid">
            <div class="leader-card">
              <div class="leader-image">
                <div class="leader-avatar">👨‍🔬</div>
              </div>
              <div class="leader-info">
                <h3>Prof. Dr. Martin Novák</h3>
                <p class="leader-title">Riaditeľ inštitútu</p>
                <p class="leader-description">
                  S viac ako 20-ročnými skúsenosťami vo vedeckom výskume vedie náš inštitút 
                  smerom k novým objavom v oblasti biotechnológií.
                </p>
                <div class="leader-credentials">
                  <span class="credential">PhD. Biotechnológie</span>
                  <span class="credential">Harvard University</span>
                </div>
              </div>
            </div>
            
            <div class="leader-card">
              <div class="leader-image">
                <div class="leader-avatar">👩‍🔬</div>
              </div>
              <div class="leader-info">
                <h3>Prof. Dr. Anna Svobodová</h3>
                <p class="leader-title">Vedúca výskumu</p>
                <p class="leader-description">
                  Špecializuje sa na environmentálne vedy a vedie tím výskumníkov 
                  pri štúdiu klimatických zmien a ich dopadov.
                </p>
                <div class="leader-credentials">
                  <span class="credential">PhD. Environmentálne vedy</span>
                  <span class="credential">MIT</span>
                </div>
              </div>
            </div>
            
            <div class="leader-card">
              <div class="leader-image">
                <div class="leader-avatar">👨‍💻</div>
              </div>
              <div class="leader-info">
                <h3>Dr. Pavel Krejčí</h3>
                <p class="leader-title">Vedúci IT a dátovej analýzy</p>
                <p class="leader-description">
                  Expert na umelú inteligenciu a big data, ktorý transformuje spôsob, 
                  akým analyzujeme a interpretujeme vedecké dáta.
                </p>
                <div class="leader-credentials">
                  <span class="credential">PhD. Informatika</span>
                  <span class="credential">Stanford University</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <FooterComponent />
  </div>
</template>

<script>
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

export default {
  name: 'AboutView',
  components: {
    NavbarComponent,
    FooterComponent
  }
}
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

/* História a vízia section */
.history-vision-section {
  padding: 6rem 0;
  background: white;
}

.content-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 3rem;
}

.content-card {
  background: var(--light-bg);
  padding: 3rem;
  border-radius: 12px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.content-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
}

.card-icon {
  font-size: 3rem;
  margin-bottom: 1.5rem;
}

.content-card h2 {
  font-size: 1.75rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
}

.content-card p {
  color: var(--text-secondary);
  margin-bottom: 1rem;
  line-height: 1.7;
}

/* Hodnoty section */
.values-section {
  padding: 6rem 0;
  background: var(--light-bg);
}

.values-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.value-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.value-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
}

.value-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.value-card h3 {
  font-size: 1.25rem;
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
  padding: 6rem 0;
  background: white;
}

.leadership-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 3rem;
}

.leader-card {
  background: var(--light-bg);
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.leader-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
}

.leader-image {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
  text-align: center;
}

.leader-avatar {
  font-size: 4rem;
}

.leader-info {
  padding: 2rem;
}

.leader-info h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.leader-title {
  color: var(--primary-color);
  font-weight: 500;
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

}

.credential {
  background: var(--primary-color);
  color: black;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Úspechy section */
.achievements-section {
  padding: 6rem 0;
  background: var(--light-bg);
}

.achievements-timeline {
  position: relative;
  max-width: 800px;
  margin: 0 auto;
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
  margin-bottom: 4rem;
  display: flex;
  align-items: center;
}

.timeline-item:nth-child(even) {
  flex-direction: row-reverse;
}

.timeline-item:nth-child(even) .timeline-content {
  text-align: right;
}

.timeline-year {
  background: var(--primary-color);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  z-index: 2;
}

.timeline-content {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  width: 45%;
  box-shadow: var(--shadow-md);
}

.timeline-content h4 {
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
  padding: 6rem 0;
  background: white;
}

.contact-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 3rem;
}

.contact-info,
.visit-info {
  background: var(--light-bg);
  padding: 3rem;
  border-radius: 12px;
}

.contact-info h3,
.visit-info h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 2rem;
}

.contact-details {
  space-y: 1.5rem;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.contact-icon {
  font-size: 1.5rem;
  margin-top: 0.25rem;
}

.contact-text strong {
  display: block;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.contact-text p {
  color: var(--text-secondary);
  line-height: 1.5;
}

.visit-schedule {
  space-y: 1rem;
}

.schedule-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid var(--border-color);
}

.schedule-item:last-child {
  border-bottom: none;
}

.day {
  font-weight: 500;
  color: var(--text-primary);
}

.time {
  color: var(--text-secondary);
}

.visit-note {
  margin-top: 2rem;
  padding: 1rem;
  background: rgba(37, 99, 235, 0.1);
  border-radius: 8px;
}

.visit-note p {
  color: var(--text-secondary);
  font-size: 0.875rem;
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
  
  .content-grid {
    grid-template-columns: 1fr;
  }
  
  .achievements-timeline::before {
    left: 2rem;
  }
  
  .timeline-item {
    flex-direction: column !important;
    align-items: flex-start;
    padding-left: 4rem;
  }
  
  .timeline-year {
    left: 2rem !important;
    transform: none !important;
  }
  
  .timeline-content {
    width: 100% !important;
    text-align: left !important;
  }
  
  .contact-grid {
    grid-template-columns: 1fr;
  }
}
</style>
