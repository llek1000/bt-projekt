<template>
  <footer class="footer">
    <div class="footer-content">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-column">
            <h3>Research Institute</h3>
            <p>University Campus</p>
            <p>1180 Vienna, Austria</p>
            <p>Tel: +43 1 47654 0</p>
            <p>Email: research@institute.ac.at</p>
          </div>
          <div class="footer-column">
            <h3>Quick Links</h3>
            <ul>
              <li><a href="/research">Research</a></li>
              <li><a href="/publications">Publications</a></li>
              <li><a href="/departments">Departments</a></li>
              <li><a href="/about">About Us</a></li>
              <li><a href="/contact">Contact</a></li>
            </ul>
          </div>
          <div class="footer-column">
            <h3>Connect With Us</h3>
            <div class="social-links">
              <a href="#" class="social-link">Facebook</a>
              <a href="#" class="social-link">Twitter</a>
              <a href="#" class="social-link">LinkedIn</a>
              <a href="#" class="social-link">YouTube</a>
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
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'Footer'
})
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Footer */
.footer {
  background-color: #333;
  color: white;
  margin-top: auto;
}

.footer-content {
  padding: 3rem 0;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.footer-column h3 {
  font-size: 1.3rem;
  margin-bottom: 1.5rem;
  color: white;
}

.footer-column p {
  margin-bottom: 0.5rem;
  color: #bbb;
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
  color: #bbb;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-column a:hover {
  color: white;
}

.social-links {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.social-link {
  color: #bbb;
  text-decoration: none;
  transition: color 0.3s ease;
  display: inline-block;
  margin-right: 1rem;
}

.social-link:hover {
  color: white;
}

.footer-bottom {
  background-color: #222;
  padding: 1.5rem 0;
}

.footer-bottom .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.footer-bottom p {
  margin: 0;
  color: #999;
}

.footer-links {
  display: flex;
  gap: 1.5rem;
}

.footer-links a {
  color: #999;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: white;
}
</style>