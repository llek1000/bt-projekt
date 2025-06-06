<template>
  <footer class="footer">
    <div class="footer-content">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-column">
            <div class="footer-logo">
              <div class="logo-wrapper">
                <div class="logo">🎓</div>
                <div class="logo-text">
                  <div class="institute-name">Univerzita Konštantína Filozofa</div>
                  <div class="institute-subtitle">Nitra</div>
                </div>
              </div>
            </div>
            <p class="footer-description">
              Univerzita Konštantína Filozofa v Nitre je moderná univerzita 
              s dlhoročnou tradíciou, ktorá poskytuje kvalitné vysokoškolské 
              vzdelávanie a vedecký výskum.
            </p>
            <div class="contact-info">
              <div class="contact-item">
                <span class="contact-icon">📍</span>
                <span>Tr. A. Hlinku 1, 949 01 Nitra, Slovenská republika</span>
              </div>
              <div class="contact-item">
                <span class="contact-icon">📞</span>
                <span>+421 37 641 4111</span>
              </div>
              <div class="contact-item">
                <span class="contact-icon">✉️</span>
                <span>info@ukf.sk</span>
              </div>
              <div class="contact-item">
                <span class="contact-icon">🌐</span>
                <span>www.ukf.sk</span>
              </div>
            </div>
          </div>

          <div class="footer-column">
            <h3>Navigácia</h3>
            <ul class="footer-links">
              <li><router-link to="/">Domov</router-link></li>
              <li><router-link to="/about">O nás</router-link></li>
              <li><router-link to="/departments">Fakulty</router-link></li>
              <li><router-link to="/publications">Publikácie</router-link></li>
            </ul>
          </div>

          <div class="footer-column">
            <h3>Spojte sa s nami</h3>
            <div class="social-media">
              <a href="https://www.facebook.com/UKFNitra" class="social-icon facebook" aria-label="Facebook">
                <span>📘</span>
              </a>
              <a href="https://twitter.com/UKFNitra" class="social-icon twitter" aria-label="Twitter">
                <span>🐦</span>
              </a>
              <a href="https://www.linkedin.com/school/ukf-nitra" class="social-icon linkedin" aria-label="LinkedIn">
                <span>💼</span>
              </a>
              <a href="https://www.youtube.com/user/UKFNitra" class="social-icon youtube" aria-label="YouTube">
                <span>📺</span>
              </a>
              <a href="https://www.instagram.com/ukf_nitra" class="social-icon instagram" aria-label="Instagram">
                <span>📷</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="footer-bottom">
      <div class="container">
        <div class="footer-bottom-content">
          <div class="copyright">
            <p>&copy; {{ currentYear }} Univerzita Konštantína Filozofa v Nitre. Všetky práva vyhradené.</p>
          </div>
          <div class="footer-bottom-links">
            <a href="/privacy">Ochrana osobných údajov</a>
            <a href="/terms">Podmienky používania</a>
            <a href="/cookies">Politika cookies</a>
            <a href="/sitemap">Mapa stránky</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'FooterComponent',
  data() {
    return {
      newsletterEmail: '',
      currentYear: new Date().getFullYear()
    }
  },
  methods: {
    subscribeNewsletter() {
      if (this.newsletterEmail) {
        console.log('Prihlásenie k newsletteru:', this.newsletterEmail)
        alert('Ďakujeme za prihlásenie k našemu newsletteru!')
        this.newsletterEmail = ''
      }
    }
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

:root {
  --primary-color: #2563eb;
  --primary-dark: #1d4ed8;
  --accent-color: #f59e0b;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --text-light: #9ca3af;
  --bg-black: #000000;
  --bg-dark-black: #0a0a0a;
  --border-dark: #1f1f1f;
  --white: #ffffff;
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.footer {
  background: #a48df0;
  color: var(--white);
  margin-top: auto;
  position: relative;
  overflow: hidden;
}

.footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: var(--gradient-primary);
}

.footer-content {
  padding: 4rem 0 2rem;
  position: relative;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 3rem;
  margin-bottom: 2rem;
}

.footer-column h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: var(--white);
  position: relative;
}

.footer-column h3::after {
  content: '';
  position: absolute;
  bottom: -0.5rem;
  left: 0;
  width: 2rem;
  height: 2px;
  background: var(--accent-color);
}

.footer-logo {
  margin-bottom: 1.5rem;
}

.logo-wrapper {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  font-size: 2.5rem;
  filter: drop-shadow(0 0 10px rgba(245, 158, 11, 0.3));
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.institute-name {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--white);
  line-height: 1.2;
}

.institute-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: 0.875rem;
  color: var(--accent-color);
  font-weight: 500;
  margin-top: 0.25rem;
}

.footer-description {
  color: var(--text-light);
  line-height: 1.6;
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
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
  color: var(--text-light);
  font-size: 0.9rem;
}

.contact-icon {
  font-size: 1.1rem;
  width: 1.5rem;
  text-align: center;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 0.75rem;
}

.footer-links a {
  color: var(--text-light);
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  position: relative;
}

.footer-links a:hover {
  color: var(--accent-color);
  padding-left: 0.5rem;
}

.footer-links a::before {
  content: '';
  position: absolute;
  left: -0.5rem;
  top: 50%;
  transform: translateY(-50%);
  width: 0;
  height: 2px;
  background: var(--accent-color);
  transition: width 0.3s ease;
}

.footer-links a:hover::before {
  width: 0.25rem;
}

.social-media {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.social-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  color: var(--white);
  text-decoration: none;
  font-size: 1.2rem;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.social-icon:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(245, 158, 11, 0.3);
}

.social-icon.facebook:hover { background: #1877f2; }
.social-icon.twitter:hover { background: #1da1f2; }
.social-icon.linkedin:hover { background: #0077b5; }
.social-icon.youtube:hover { background: #ff0000; }
.social-icon.instagram:hover { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }

.newsletter {
  margin-top: 1.5rem;
}

.newsletter h4 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--white);
}

.newsletter p {
  color: var(--text-light);
  font-size: 0.9rem;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.newsletter-form {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.newsletter-input {
  flex: 1;
  min-width: 200px;
  padding: 0.75rem 1rem;
  border: 1px solid var(--border-dark);
  border-radius: 6px;
  background: rgba(255, 255, 255, 0.05);
  color: var(--white);
  font-size: 0.9rem;
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
}

.newsletter-input::placeholder {
  color: var(--text-light);
}

.newsletter-input:focus {
  outline: none;
  border-color: var(--accent-color);
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
  background: rgba(255, 255, 255, 0.1);
}

.newsletter-btn {
  padding: 0.75rem 1.5rem;
  background: var(--gradient-primary);
  color: var(--white);
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.newsletter-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(245, 158, 11, 0.3);
}

.footer-bottom {
  background: var(--bg-dark-black);
  padding: 1.5rem 0;
  border-top: 1px solid var(--border-dark);
}

.footer-bottom-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.copyright {
  color: var(--text-light);
  font-size: 0.9rem;
}

.footer-bottom-links {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.footer-bottom-links a {
  color: var(--text-light);
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.footer-bottom-links a:hover {
  color: var(--accent-color);
}


@media (max-width: 768px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .footer-content {
    padding: 3rem 0 1.5rem;
  }
  
  .footer-bottom-content {
    flex-direction: column;
    text-align: center;
  }
  
  .footer-bottom-links {
    justify-content: center;
    gap: 1rem;
  }
  
  .newsletter-form {
    flex-direction: column;
  }
  
  .newsletter-input {
    min-width: auto;
  }
  
  .social-media {
    justify-content: center;
  }
  
  .logo-wrapper {
    justify-content: center;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 1rem;
  }
  
  .footer-bottom-links {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .institute-name {
    font-size: 1.25rem;
  }
  
  .logo {
    font-size: 2rem;
  }
}
</style>