<template>
  <div class="page-container">
    <!-- Použitie spoločného navbar komponentu -->
    <NavbarComponent />
    
    <!-- Hlavný obsah -->
    <main class="main-content">
      <!-- Login Hero sekcia -->
      <section class="login-hero-section">
        <div class="hero-background">
          <div class="hero-overlay"></div>
          <div class="hero-particles"></div>
        </div>
        <div class="login-container">
          <div class="login-form-container">
            <form @submit.prevent="login" class="login-form">
              <div class="login-header">
                <h2>Prihlásenie</h2>
                <p class="login-subtitle">Vstúpte do svojho účtu</p>
              </div>

              <!-- Error Alert -->
              <div v-if="error" class="error-alert">
                {{ error }}
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input
                  type="email"
                  id="email"
                  v-model="credentials.email"
                  required
                  :class="{ error: fieldErrors.email }"
                />
                <div v-if="fieldErrors.email" class="error-message">
                  {{ fieldErrors.email }}
                </div>
              </div>

              <div class="form-group">
                <label for="password">Heslo</label>
                <input
                  type="password"
                  id="password"
                  v-model="credentials.password"
                  required
                  :class="{ error: fieldErrors.password }"
                />
                <div v-if="fieldErrors.password" class="error-message">
                  {{ fieldErrors.password }}
                </div>
              </div>

              <button type="submit" class="login-button" :disabled="isLoading">
                <span v-if="isLoading">Prihlasuje sa...</span>
                <span v-else>Prihlásiť sa</span>
                <span class="btn-arrow">→</span>
              </button>

              <div class="login-links">
                <a href="#" class="login-link">Zabudli ste heslo?</a>
              </div>
            </form>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <FooterComponent />
  </div>
</template>

<script>
import authentification from '@/services/authentification';
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

export default {
  name: "LoginView",
  components: {
    NavbarComponent,
    FooterComponent
  },
  data() {
    return {
      credentials: {
        email: '',
        password: ''
      },
      error: '',
      fieldErrors: {},
      isLoading: false
    }
  },
  methods: {
    async login() {
      this.isLoading = true;
      this.error = '';
      this.fieldErrors = {};

      try {
        const response = await authentification.login(this.credentials);
        
        if (response.data.success) {
          // Handle successful login
          const redirectUrl = response.data.redirect_url || '/';
          this.$router.push(redirectUrl);
        }
      } catch (error) {
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            this.fieldErrors = error.response.data.errors;
          } else {
            this.error = error.response.data.message || 'Prihlásenie zlyhalo';
          }
        } else {
          this.error = 'Nastala chyba pri prihlasovaní';
        }
      } finally {
        this.isLoading = false;
      }
    }
  }
};
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
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Base Styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.page-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: 'Inter', sans-serif;
  color: var(--text-primary);
}

.main-content {
  flex: 1;
}

/* Login Hero sekcia */
.login-hero-section {
  min-height: calc(100vh - 140px);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
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

.login-container {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 500px;
  padding: 2rem;
}

.login-form-container {
  background: white;
  border-radius: 12px;
  padding: 3rem;
  box-shadow: var(--shadow-lg);
}

.login-form {
  width: 100%;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.login-subtitle {
  color: var(--text-secondary);
  font-size: 1rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.form-group input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-group input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-group input.error {
  border-color: #ef4444;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.error-alert {
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  text-align: center;
}

.login-button {
  width: 100%;
  background: var(--primary-color);
  color: black;
  border: none;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.login-button:hover:not(:disabled) {
  background: var(--primary-dark);
  transform: translateY(-1px);
}

.login-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.btn-arrow {
  transition: transform 0.3s ease;
}

.login-button:hover .btn-arrow {
  transform: translateX(3px);
}

.login-links {
  display: flex;
  justify-content: center;
  align-items: center;
}

.login-link {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.login-link:hover {
  color: var(--primary-dark);
}

.login-link::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 2px;
  background: var(--primary-color);
  transition: width 0.3s ease;
}

.login-link:hover::after {
  width: 100%;
}

/* Responsive Design */
@media (max-width: 768px) {
  .login-container {
    padding: 1rem;
  }
  
  .login-form-container {
    padding: 2rem;
  }
  
  .login-header h2 {
    font-size: 1.75rem;
  }
  
  .login-links {
    flex-direction: column;
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .login-form-container {
    padding: 1.5rem;
  }
  
  .login-header h2 {
    font-size: 1.5rem;
  }
}
</style>
