<template>
  <div class="login-container">
    <div class="login-form-container">
      <div class="login-form">
        <h2>Login</h2>
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            v-model="email" 
            placeholder="Enter email"
            :class="{ error: submitted && !email }"
          />
          <span v-if="submitted && !email" class="error-message">Email is required</span>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            v-model="password" 
            placeholder="Enter password"
            :class="{ error: submitted && !password }"
          />
          <span v-if="submitted && !password" class="error-message">Password is required</span>
        </div>
        
        <div v-if="error" class="error-alert">{{ error }}</div>
        
        <button @click="login" class="login-button" :disabled="loading">
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
        
        <div class="links">
          <a href="#">Forgot Password?</a>
          <a href="#">Register</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import authentification from '@/services/authentification';

export default {
  name: "LoginView",
  data() {
    return {
      email: "",
      password: "",
      submitted: false,
      loading: false,
      error: null
    };
  },
  methods: {
   login() {
      this.submitted = true;
      this.error = null;
      
      if (!this.email || !this.password) {
        return;
      }
      
      this.loading = true;
      
      authentification.login({
        email: this.email,
        password: this.password
      })
        .then(response => {
          // Navigate after successful login
          this.$router.push('/dashboard');
        })
        .catch(error => {
          // Handle UI error display
          this.error = error.response?.data?.message || 'Login failed. Please try again.';
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>

<style>
.login-container {
  display: flex;
  height: 100vh;
  width: 100%;
  background-color: #f5f5f5;
  background-image: linear-gradient(135deg, #4a90e2, #2c3e50);
}

.login-form-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}

.login-form {
  width: 80%;
  max-width: 450px;
  padding: 3rem;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  
  h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: #333;
  }
}

.form-group {
  margin-bottom: 1.5rem;
  
  label {
    display: block;
    margin-bottom: 0.5rem;
    color: #555;
  }
  
  input {
    width: 100%;
    padding: 0.75rem;
    font-size: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    
    &:focus {
      outline: none;
      border-color: #4a90e2;
    }
    
    &.error {
      border-color: #e74c3c;
    }
  }
  
  .error-message {
    color: #e74c3c;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
  }
}

.error-alert {
  padding: 0.75rem;
  margin-bottom: 1rem;
  background-color: #f8d7da;
  color: #721c24;
  border-radius: 4px;
  text-align: center;
}

.login-button {
  width: 100%;
  padding: 0.75rem;
  background-color: #4a90e2;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
  
  &:hover {
    background-color: #3a80d2;
  }
  
  &:disabled {
    background-color: #95b7e1;
    cursor: not-allowed;
  }
}

.links {
  display: flex;
  justify-content: space-between;
  margin-top: 1.5rem;
  
  a {
    color: #4a90e2;
    text-decoration: none;
    font-size: 0.9rem;
    
    &:hover {
      text-decoration: underline;
    }
  }
}

@media (max-width: 576px) {
  .login-form {
    width: 90%;
    padding: 2rem;
  }
}
</style>