<template>
  <div class="login-page">
    <NavbarComponent />
    
    <div class="background-elements">
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
      <div class="floating-shape shape-3"></div>
      <div class="floating-dots">
        <div class="dot" v-for="n in 12" :key="n"></div>
      </div>
    </div>

    <div class="login-container">
      <div class="login-card">

        <div class="login-header">
          <div class="logo-section">
            <div class="logo-icon">
              <i class="fas fa-graduation-cap"></i>
            </div>
          </div>
          <h2 class="login-title">Prihlásenie do systému</h2>
          <p class="login-subtitle">Zadajte svoje prihlasovacie údaje na pokračovanie</p>
        </div>
        

        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email" class="form-label">
              <i class="fas fa-envelope"></i>
              Email adresa
            </label>
            <div class="input-wrapper">
              <input
                type="email"
                id="email"
                v-model="form.email"
                required
                :disabled="isLoading"
                placeholder="example@university.sk"
                class="form-input"
                autocomplete="email"
              />
              <div class="input-focus-border"></div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="password" class="form-label">
              <i class="fas fa-lock"></i>
              Heslo
            </label>
            <div class="input-wrapper">
              <input
                :type="showPassword ? 'text' : 'password'"
                id="password"
                v-model="form.password"
                required
                :disabled="isLoading"
                placeholder="Zadajte vaše heslo"
                class="form-input"
                autocomplete="current-password"
              />
              <button
                type="button"
                @click="togglePassword"
                class="password-toggle"
                :disabled="isLoading"
                tabindex="-1"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
              <div class="input-focus-border"></div>
            </div>
          </div>
          

          <div class="form-group checkbox-group">
            <label class="checkbox-label">
              <input
                type="checkbox"
                v-model="rememberMe"
                :disabled="isLoading"
                class="checkbox-input"
              />
            </label>
          </div>

          <div v-if="errorMessage" class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ errorMessage }}</span>
          </div>
     
          <div v-if="successMessage" class="success-message">
            <i class="fas fa-check-circle"></i>
            <span>{{ successMessage }}</span>
          </div>
   
          <button 
            type="submit" 
            class="login-btn"
            :disabled="isLoading || !form.email || !form.password"
          >
            <div class="btn-content">
              <i v-if="isLoading" class="fas fa-spinner spinning"></i>
              <i v-else class="fas fa-sign-in-alt"></i>
              <span v-if="isLoading">Prihlasovanie...</span>
              <span v-else>Prihlásiť sa</span>
            </div>
            <div class="btn-overlay"></div>
          </button>
        </form>    
      </div>
    </div>
    
    <div v-if="showForgotPasswordModal" class="modal-overlay" @click="closeForgotPassword">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Obnovenie hesla</h3>
          <button @click="closeForgotPassword" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Kontaktujte administrátora systému pre obnovenie hesla.</p>
          <div class="contact-info">
            <p><i class="fas fa-envelope"></i> admin@university.sk</p>
            <p><i class="fas fa-phone"></i> +421 123 456 789</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import auth from '@/services/authentification'
import NavbarComponent from '@/components/NavbarComponent.vue'

export default defineComponent({
  name: 'LoginView',
  
  components: {
    NavbarComponent
  },
  
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      rememberMe: false,
      showPassword: false,
      errorMessage: '',
      successMessage: '',
      isLoading: false,
      showForgotPasswordModal: false
    }
  },
  
  mounted() {
    this.animateElements()
  },
  
  methods: {
    async handleLogin() {
      this.isLoading = true
      this.errorMessage = ''
      this.successMessage = ''
      
      try {
        const response = await auth.login(this.form)
        
        this.successMessage = 'Prihlásenie úspešné! Presmerovávame...'
        
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        const redirectUrl = localStorage.getItem('redirectAfterLogin')
        localStorage.removeItem('redirectAfterLogin')
        
        if (redirectUrl) {
          this.$router.push(redirectUrl)
        } else if (response.data.redirect_url) {
          this.$router.push(response.data.redirect_url)
        } else {
          const redirectPath = await auth.getRedirectUrl()
          this.$router.push(redirectPath)
        }
        
      } catch (error: any) {
        console.error('Login error:', error)
        
        if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message
        } else if (error.response?.data?.errors) {
          const errors = Object.values(error.response.data.errors).flat()
          this.errorMessage = errors.join(', ')
        } else {
          this.errorMessage = 'Nastala chyba pri prihlasovaní. Skúste to znovu.'
        }
        

        this.shakeForm()
      } finally {
        this.isLoading = false
      }
    },
    
    togglePassword() {
      this.showPassword = !this.showPassword
    },
    
    showForgotPassword() {
      this.showForgotPasswordModal = true
    },
    
    closeForgotPassword() {
      this.showForgotPasswordModal = false
    },
    
    animateElements() {

      const shapes = document.querySelectorAll('.floating-shape')
      shapes.forEach((shape, index) => {
        setTimeout(() => {
          shape.classList.add('animate')
        }, index * 200)
      })
    },
    
    shakeForm() {
      const card = document.querySelector('.login-card')
      if (card) {
        card.classList.add('shake')
        setTimeout(() => {
          card.classList.remove('shake')
        }, 600)
      }
    }
  }
})
</script>

<style scoped>

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap');


:root {
  --primary-color: #667eea;
  --primary-dark: #5a67d8;
  --primary-light: #7c91f7;
  --secondary-color: #764ba2;
  --accent-color: #f093fb;
  --success-color: #48bb78;
  --error-color: #f56565;
  --warning-color: #ed8936;
  --text-primary: #2d3748;
  --text-secondary: #4a5568;
  --text-light: #718096;
  --text-white: #ffffff;
  --bg-primary: #ffffff;
  --bg-secondary: #f7fafc;
  --bg-dark: #1a202c;
  --border-color: #e2e8f0;
  --border-light: #edf2f7;
  --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-2xl: 24px;
}


* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.login-page {
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
  position: relative;
  overflow: hidden;
}


.background-elements {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 50%, var(--accent-color) 100%);
  z-index: -1;
}

.floating-shape {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 120px;
  height: 120px;
  top: 15%;
  left: 8%;
  animation-delay: 0s;
}

.shape-2 {
  width: 180px;
  height: 180px;
  top: 65%;
  right: 10%;
  animation-delay: 2s;
}

.shape-3 {
  width: 90px;
  height: 90px;
  bottom: 15%;
  left: 15%;
  animation-delay: 4s;
}

.floating-dots {
  position: absolute;
  width: 100%;
  height: 100%;
}

.dot {
  position: absolute;
  width: 5px;
  height: 5px;
  background: rgba(255, 255, 255, 0.4);
  border-radius: 50%;
  animation: twinkle 3s ease-in-out infinite;
}

.dot:nth-child(1) { top: 20%; left: 20%; animation-delay: 0s; }
.dot:nth-child(2) { top: 80%; left: 80%; animation-delay: 1s; }
.dot:nth-child(3) { top: 40%; left: 70%; animation-delay: 2s; }
.dot:nth-child(4) { top: 70%; left: 30%; animation-delay: 3s; }
.dot:nth-child(5) { top: 30%; left: 50%; animation-delay: 4s; }
.dot:nth-child(6) { top: 60%; left: 10%; animation-delay: 5s; }
.dot:nth-child(7) { top: 25%; left: 85%; animation-delay: 6s; }
.dot:nth-child(8) { top: 85%; left: 25%; animation-delay: 7s; }
.dot:nth-child(9) { top: 45%; left: 15%; animation-delay: 8s; }
.dot:nth-child(10) { top: 75%; left: 65%; animation-delay: 9s; }
.dot:nth-child(11) { top: 35%; left: 35%; animation-delay: 10s; }
.dot:nth-child(12) { top: 55%; left: 90%; animation-delay: 11s; }

@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  33% { transform: translateY(-30px) rotate(120deg); }
  66% { transform: translateY(30px) rotate(240deg); }
}

@keyframes twinkle {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.5); }
}


.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  position: relative;
  z-index: 1;
}

.login-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(30px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  padding: 3rem;
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-2xl);
  width: 100%;
  max-width: 480px;
  position: relative;
  overflow: hidden;
  transform: translateY(0);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.login-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(90deg, var(--primary-color), var(--accent-color), var(--secondary-color));
  border-radius: var(--radius-2xl) var(--radius-2xl) 0 0;
}

.login-card::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: conic-gradient(from 0deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  animation: rotate 10s linear infinite;
  pointer-events: none;
  opacity: 0.5;
}

@keyframes rotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.login-card.shake {
  animation: shake 0.6s ease-in-out;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-15px); }
  75% { transform: translateX(15px); }
}


.login-header {
  text-align: center;
  margin-bottom: 2.5rem;
  position: relative;
  z-index: 2;
}

.logo-section {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.logo-icon {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border-radius: var(--radius-xl);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2.2rem;
  box-shadow: var(--shadow-lg);
  position: relative;
  overflow: hidden;
}

.logo-icon::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transform: rotate(45deg);
  animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
  50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
  100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

.system-title {
  font-family: 'Poppins', sans-serif;
  font-size: 2.8rem;
  font-weight: 800;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color), var(--accent-color));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.login-title {
  font-family: 'Poppins', sans-serif;
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.75rem;
}

.login-subtitle {
  color: var(--text-secondary);
  font-size: 1.1rem;
  font-weight: 400;
  line-height: 1.5;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
  position: relative;
  z-index: 2;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 600;
  color: var(--text-primary);
  font-size: 0.95rem;
}

.form-label i {
  color: var(--primary-color);
  width: 18px;
  font-size: 1.1rem;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.form-input {
  width: 100%;
  padding: 1.25rem 1.5rem;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-xl);
  font-size: 1.1rem;
  font-weight: 400;
  color: var(--text-primary);
  background: var(--bg-primary);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  outline: none;
  box-shadow: var(--shadow-sm);
}

.form-input:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 6px rgba(102, 126, 234, 0.15), var(--shadow-md);
  transform: translateY(-2px);
}

.form-input:disabled {
  background-color: var(--bg-secondary);
  cursor: not-allowed;
  opacity: 0.7;
}

.form-input::placeholder {
  color: var(--text-light);
  font-weight: 400;
}

.input-focus-border {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
  transform: scaleX(0);
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 0 0 var(--radius-xl) var(--radius-xl);
}

.form-input:focus + .password-toggle + .input-focus-border,
.form-input:focus + .input-focus-border {
  transform: scaleX(1);
}

.password-toggle {
  position: absolute;
  right: 1.25rem;
  background: none;
  border: none;
  color: var(--text-light);
  cursor: pointer;
  padding: 0.75rem;
  border-radius: var(--radius-md);
  transition: all 0.3s ease;
  z-index: 1;
  font-size: 1.1rem;
}

.password-toggle:hover {
  color: var(--primary-color);
  background: rgba(102, 126, 234, 0.1);
  transform: scale(1.1);
}

.password-toggle:disabled {
  cursor: not-allowed;
  opacity: 0.5;
  transform: none;
}

.checkbox-group {
  flex-direction: row;
  align-items: center;
  margin-top: 0.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 1rem;
  cursor: pointer;
  user-select: none;
  transition: all 0.3s ease;
}

.checkbox-label:hover {
  transform: translateX(2px);
}

.checkbox-input {
  display: none;
}

.checkbox-custom {
  width: 24px;
  height: 24px;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-md);
  position: relative;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background: var(--bg-primary);
  box-shadow: var(--shadow-sm);
}

.checkbox-input:checked + .checkbox-custom {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border-color: var(--primary-color);
  transform: scale(1.1);
}

.checkbox-input:checked + .checkbox-custom::after {
  content: '\f00c';
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 14px;
}

.checkbox-text {
  color: var(--text-secondary);
  font-size: 1rem;
  font-weight: 500;
}

.error-message,
.success-message {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  border-radius: var(--radius-xl);
  font-weight: 600;
  animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: var(--shadow-md);
}

.error-message {
  background: rgba(245, 101, 101, 0.1);
  border: 2px solid rgba(245, 101, 101, 0.3);
  color: var(--error-color);
}

.success-message {
  background: rgba(72, 187, 120, 0.1);
  border: 2px solid rgba(72, 187, 120, 0.3);
  color: var(--success-color);
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-15px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}


.login-btn {
  position: relative;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: var(--text-white);
  padding: 1.25rem 2rem;
  border: none;
  border-radius: var(--radius-xl);
  font-size: 1.2rem;
  font-weight: 700;
  cursor: pointer;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: var(--shadow-lg);
  margin-top: 1rem;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: var(--shadow-2xl);
}

.login-btn:active {
  transform: translateY(-1px);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.btn-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  position: relative;
  z-index: 1;
}

.btn-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, var(--accent-color), var(--primary-light));
  opacity: 0;
  transition: opacity 0.4s ease;
}

.login-btn:hover .btn-overlay {
  opacity: 1;
}

.spinning {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}


.login-footer {
  margin-top: 2.5rem;
  text-align: center;
  position: relative;
  z-index: 2;
}

.divider {
  position: relative;
  margin: 2rem 0;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--border-color), transparent);
}

.divider span {
  background: var(--bg-primary);
  padding: 0 1.5rem;
  color: var(--text-light);
  font-size: 1rem;
  font-weight: 500;
  position: relative;
}

.footer-links {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.footer-link {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 1rem;
  font-weight: 500;
  padding: 1rem;
  border-radius: var(--radius-lg);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid transparent;
}

.footer-link:hover {
  background: rgba(102, 126, 234, 0.1);
  color: var(--primary-color);
  border-color: rgba(102, 126, 234, 0.2);
  transform: translateY(-2px);
}

.forgot-password {
  color: var(--primary-color);
  font-weight: 600;
}


.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease;
}

.modal-content {
  background: var(--bg-primary);
  border-radius: var(--radius-2xl);
  padding: 2.5rem;
  max-width: 450px;
  width: 90%;
  box-shadow: var(--shadow-2xl);
  animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid var(--border-light);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.modal-header h3 {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--text-primary);
  font-family: 'Poppins', sans-serif;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-light);
  cursor: pointer;
  padding: 0.75rem;
  border-radius: var(--radius-md);
  transition: all 0.3s ease;
}

.close-btn:hover {
  color: var(--text-primary);
  background: var(--bg-secondary);
  transform: scale(1.1);
}

.modal-body p {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

.contact-info {
  background: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: var(--radius-lg);
  border: 1px solid var(--border-light);
}

.contact-info p {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  font-size: 1rem;
  font-weight: 500;
}

.contact-info p:last-child {
  margin-bottom: 0;
}

.contact-info i {
  color: var(--primary-color);
  width: 20px;
  font-size: 1.1rem;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}


@media (max-width: 768px) {
  .login-container {
    padding: 1.5rem;
  }
  
  .login-card {
    padding: 2.5rem;
    max-width: none;
  }
  
  .system-title {
    font-size: 2.2rem;
  }
  
  .login-title {
    font-size: 1.75rem;
  }
  
  .logo-icon {
    width: 60px;
    height: 60px;
    font-size: 2rem;
  }
}

@media (max-width: 480px) {
  .login-card {
    padding: 2rem;
  }
  
  .logo-section {
    flex-direction: column;
    gap: 1rem;
  }
  
  .system-title {
    font-size: 2rem;
  }
  
  .login-title {
    font-size: 1.5rem;
  }
  
  .form-input {
    padding: 1rem 1.25rem;
    font-size: 1rem;
  }
  
  .login-btn {
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}


@media (prefers-contrast: high) {
  .login-card {
    border: 3px solid var(--text-primary);
  }
  
  .form-input {
    border-width: 3px;
  }
}

.login-btn:focus-visible,
.form-input:focus-visible,
.footer-link:focus-visible,
.checkbox-label:focus-visible {
  outline: 3px solid var(--primary-color);
  outline-offset: 3px;
}
</style>
