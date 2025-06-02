<template>
  <div class="department-page">
    <!-- Department Header -->
    <section class="department-header">
      <div class="department-image">
        <img :src="department.image" :alt="department.name">
        <div class="department-title">
          <h1>{{ department.name }}</h1>
          <p>{{ department.description }}</p>
        </div>
      </div>
    </section>
    
    <!-- Department Navigation -->
    <div class="department-nav">
      <div class="container">
        <button 
          @click="setActiveTab('overview')" 
          :class="['tab-button', { active: activeTab === 'overview' }]"
        >
          Overview
        </button>
        <button 
          @click="setActiveTab('research')" 
          :class="['tab-button', { active: activeTab === 'research' }]"
        >
          Research Areas
        </button>
        <button 
          @click="setActiveTab('faculty')" 
          :class="['tab-button', { active: activeTab === 'faculty' }]"
        >
          Faculty & Staff
        </button>
        <button 
          @click="setActiveTab('programs')" 
          :class="['tab-button', { active: activeTab === 'programs' }]"
        >
          Programs
        </button>
        <button 
          @click="setActiveTab('publications')" 
          :class="['tab-button', { active: activeTab === 'publications' }]"
        >
          Publications
        </button>
        <button 
          @click="setActiveTab('contact')" 
          :class="['tab-button', { active: activeTab === 'contact' }]"
        >
          Contact
        </button>
      </div>
    </div>
    
    <!-- Department Content -->
    <main class="department-content">
      <div class="container">
        <!-- Overview Tab -->
        <section v-if="activeTab === 'overview'" class="content-section">
          <h2>Department Overview</h2>
          <div class="overview-content">
            <p>{{ department.overview }}</p>
            
            <div class="overview-highlights">
              <div class="highlight-card">
                <div class="highlight-icon">🎓</div>
                <h3>Education</h3>
                <p>Offering comprehensive programs at bachelor's, master's, and doctoral levels.</p>
              </div>
              <div class="highlight-card">
                <div class="highlight-icon">🔬</div>
                <h3>Research</h3>
                <p>Conducting innovative research with real-world applications for animal sciences.</p>
              </div>
              <div class="highlight-card">
                <div class="highlight-icon">🌱</div>
                <h3>Sustainability</h3>
                <p>Committed to developing environmentally responsible farming practices.</p>
              </div>
            </div>
            
            <div class="department-stats">
              <div class="stat-item">
                <span class="stat-number">{{ department.faculty.length }}+</span>
                <span class="stat-label">Faculty Members</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">250</span>
                <span class="stat-label">Students</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">15</span>
                <span class="stat-label">Research Labs</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">{{ articles.length }}+</span>
                <span class="stat-label">Published Articles</span>
              </div>
            </div>
          </div>
        </section>
        
        <!-- Research Areas Tab -->
        <section v-if="activeTab === 'research'" class="content-section">
          <h2>Research Areas</h2>
          <div class="research-areas-grid">
            <div 
              v-for="(area, index) in department.researchAreas" 
              :key="index" 
              class="research-area-card"
            >
              <h3>{{ area.title }}</h3>
              <p>{{ area.description }}</p>
              <a href="#" class="learn-more">Learn more</a>
            </div>
          </div>
          
          <div class="research-facilities">
            <h3>Research Facilities</h3>
            <p>Our department is equipped with state-of-the-art facilities that support our diverse research initiatives:</p>
            <ul class="facilities-list">
              <li>Genomics and Bioinformatics Laboratory</li>
              <li>Animal Nutrition Analysis Center</li>
              <li>Livestock Behavior Assessment Facility</li>
              <li>Experimental Farm and Field Research Station</li>
              <li>Climate-Controlled Housing Systems</li>
            </ul>
          </div>
        </section>
        
        <!-- Faculty Tab -->
        <section v-if="activeTab === 'faculty'" class="content-section">
          <h2>Faculty & Staff</h2>
          <div class="faculty-grid">
            <div 
              v-for="(member, index) in department.faculty" 
              :key="index" 
              class="faculty-card"
            >
              <div class="faculty-image">
                <img :src="member.image" :alt="member.name">
              </div>
              <div class="faculty-info">
                <h3>{{ member.name }}</h3>
                <p class="faculty-title">{{ member.title }}</p>
                <p class="faculty-specialty">Specialty: {{ member.specialty }}</p>
                <a href="#" class="faculty-profile-link">View Profile</a>
              </div>
            </div>
          </div>
        </section>
        
        <!-- Programs Tab -->
        <section v-if="activeTab === 'programs'" class="content-section">
          <h2>Educational Programs</h2>
          <div class="programs-list">
            <div 
              v-for="(program, index) in department.programs" 
              :key="index" 
              class="program-item"
            >
              <h3>{{ program.title }}</h3>
              <p>{{ program.description }}</p>
              <div class="program-details">
                <a href="#" class="program-link">Program Details</a>
                <a href="#" class="application-link">How to Apply</a>
              </div>
            </div>
          </div>
          
          <div class="additional-offerings">
            <h3>Additional Educational Offerings</h3>
            <ul class="offerings-list">
              <li>Professional Development Workshops</li>
              <li>Industry-Focused Certificate Programs</li>
              <li>Summer Research Opportunities for Students</li>
              <li>International Exchange Programs</li>
            </ul>
          </div>
        </section>
        
        <!-- Publications Tab -->
        <section v-if="activeTab === 'publications'" class="content-section">
          <!-- Article Reader View -->
          <div v-if="selectedArticle" class="article-reader">
            <button @click="backToPublications" class="back-button">
              ← Back to Publications
            </button>
            
            <article class="article-content">
              <header class="article-header">
                <h1>{{ selectedArticle.title }}</h1>
                <div class="article-meta">
                  <p class="article-authors">{{ selectedArticle.author_name }}</p>
                  <p class="article-publication">
                    <span class="journal">
                      {{ selectedArticle.conference_year ? 
                          formatConferenceYear(selectedArticle.conference_year) : 
                          'Conference Proceedings' }}
                    </span> •
                    <span class="year">
                      {{ selectedArticle.conference_year?.year || new Date().getFullYear() }}
                    </span>
                  </p>
                  <p class="article-doi">DOI: 10.1234/article.{{ selectedArticle.id }}</p>
                  <p class="article-date">
                    Published: {{ formatDate(selectedArticle.created_at) }}
                  </p>
                </div>
              </header>
              
              <div class="article-body">
                <section class="abstract-section">
                  <h2>Abstract</h2>
                  <p>{{ formatArticleSummary(selectedArticle) }}</p>
                </section>
                
                <section class="fulltext-section">
                  <h2>Full Text</h2>
                  <div class="article-text" v-html="selectedArticle.content || 'Full text content not available.'">
                  </div>
                </section>
              </div>
            </article>
          </div>
          
          <!-- Publications List View -->
          <div v-else>
            <div class="publications-header">
              <h2>Publications</h2>
              
              <div class="publications-controls">
                <!-- Search Input -->
                <div class="search-controls">
                  <input 
                    v-model="searchQuery" 
                    @keyup.enter="searchArticles"
                    type="text" 
                    placeholder="Search articles..." 
                    class="search-input"
                  >
                  <button @click="searchArticles" class="search-button">Search</button>
                  <button v-if="searchQuery" @click="clearSearch" class="clear-button">Clear</button>
                </div>
                
                <!-- Year Filter -->
                <div class="year-filter">
                  <label for="year-select">Filter by Year:</label>
                  <select id="year-select" v-model="selectedYear" class="year-dropdown">
                    <option value="all">All Years</option>
                    <option v-for="year in availableYears" :key="year" :value="year">
                      {{ year }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
            
            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
              <p>Loading publications...</p>
            </div>
            
            <!-- Error State -->
            <div v-else-if="error" class="error-state">
              <p>{{ error }}</p>
              <button @click="fetchArticles" class="retry-button">Retry</button>
            </div>
            
            <!-- Publications List -->
            <div v-else class="publications-list">
              <div 
                v-for="publication in publicationsFromArticles" 
                :key="publication.id" 
                class="publication-item"
              >
                <h3>{{ publication.title }}</h3>
                <p class="publication-authors">{{ publication.authors }}</p>
                <p class="publication-details">
                  <span class="journal">{{ publication.journal }}</span>,
                  <span class="year">{{ publication.year }}</span>
                </p>
                <p class="publication-doi">DOI: {{ publication.doi }}</p>
                <p class="publication-date">
                  Published: {{ formatDate(publication.created_at) }}
                </p>
                <p class="publication-abstract">{{ publication.abstract }}</p>
                <div class="publication-actions">
                  <button @click="selectArticle(publication)" class="read-article-btn">
                    Read Full Article
                  </button>
                </div>
              </div>
            </div>
            
            <div v-if="!loading && !error && publicationsFromArticles.length === 0" class="no-publications">
              <p v-if="searchQuery">No publications found for "{{ searchQuery }}".</p>
              <p v-else-if="selectedYear !== 'all'">No publications found for {{ selectedYear }}.</p>
              <p v-else>No publications available.</p>
            </div>
            
            <div v-if="publicationsFromArticles.length > 0" class="publications-summary">
              <p>Showing {{ publicationsFromArticles.length }} of {{ articles.length }} publications</p>
            </div>
          </div>
        </section>
        
        <!-- Contact Tab -->
        <section v-if="activeTab === 'contact'" class="content-section">
          <h2>Contact Information</h2>
          <div class="contact-container">
            <div class="contact-details">
              <div class="contact-group">
                <h3>Address</h3>
                <p>{{ department.contact.address }}</p>
              </div>
              
              <div class="contact-group">
                <h3>Email</h3>
                <p><a :href="`mailto:${department.contact.email}`">{{ department.contact.email }}</a></p>
              </div>
              
              <div class="contact-group">
                <h3>Phone</h3>
                <p>{{ department.contact.phone }}</p>
              </div>
              
              <div class="contact-group">
                <h3>Office Hours</h3>
                <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
              </div>
            </div>
            
            <div class="contact-form">
              <h3>Send Us a Message</h3>
              <form>
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" id="name" placeholder="Your name">
                </div>
                
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" id="email" placeholder="Your email">
                </div>
                
                <div class="form-group">
                  <label for="subject">Subject</label>
                  <input type="text" id="subject" placeholder="Message subject">
                </div>
                
                <div class="form-group">
                  <label for="message">Message</label>
                  <textarea id="message" rows="5" placeholder="Your message"></textarea>
                </div>
                
                <button type="submit" class="submit-button">Send Message</button>
              </form>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script>
import { articleApi, articleHelpers } from '@/services/article'
import { conferenceYearApi, conferenceYearHelpers } from '@/services/conferenceYear'

export default {
  name: 'DepartmentView',
  
  data() {
    return {
      // State management
      loading: false,
      error: null,
      
      // Department data (static for now - could be moved to API later)
      department: {
        name: 'Department of Animal Science',
        image: '/test.png',
        description: 'Advancing knowledge in animal health, nutrition, and sustainable farming practices through innovative research and education.',
        overview: 'The Department of Animal Science is dedicated to advancing the understanding of domesticated animals and their interactions with humans and the environment. Our faculty conducts cutting-edge research in genetics, nutrition, physiology, behavior, and sustainable farming systems.',
        researchAreas: [
          {
            title: 'Animal Genetics and Breeding',
            description: 'Developing advanced breeding methods and studying genetic diversity to improve animal health and productivity.'
          },
          {
            title: 'Animal Nutrition',
            description: 'Researching optimal feeding strategies and nutritional requirements for various species and production systems.'
          },
          {
            title: 'Animal Health and Welfare',
            description: 'Investigating disease prevention, welfare assessment, and housing systems to ensure optimal animal wellbeing.'
          },
          {
            title: 'Sustainable Livestock Systems',
            description: 'Developing environmentally friendly practices that balance productivity with ecological responsibility and resource conservation.'
          }
        ],
        faculty: [
          {
            name: 'Prof. Dr. Maria Schmidt',
            title: 'Department Chair',
            specialty: 'Animal Reproduction',
            image: '/test.png'
          },
          {
            name: 'Dr. Thomas Weber',
            title: 'Associate Professor',
            specialty: 'Animal Nutrition',
            image: '/test.png'
          },
          {
            name: 'Dr. Sarah Johnson',
            title: 'Assistant Professor',
            specialty: 'Animal Genetics',
            image: '/test.png'
          },
          {
            name: 'Dr. Michael Chen',
            title: 'Associate Professor',
            specialty: 'Animal Welfare',
            image: '/test.png'
          }
        ],
        programs: [
          {
            title: 'Bachelor of Science in Animal Science',
            description: 'A comprehensive undergraduate program covering fundamental principles of animal biology, nutrition, production, and management.'
          },
          {
            title: 'Master of Science in Animal Science',
            description: 'An advanced degree focusing on specialized areas within animal science, with emphasis on research methods and critical analysis.'
          },
          {
            title: 'PhD in Animal Science',
            description: 'A research-intensive doctoral program developing scholars who will advance the field through original contributions and innovations.'
          }
        ],
        contact: {
          address: 'Institute of Animal Science, University Campus, 1180 Vienna, Austria',
          email: 'animalscience@university.ac.at',
          phone: '+43 1 47654 3900',
          office: 'Schwackhöfer-Haus, 3rd Floor'
        }
      },
      
      // API-driven data
      articles: [],
      conferenceYears: [],
      activeTab: 'overview',
      selectedYear: 'all',
      selectedArticle: null,
      searchQuery: ''
    }
  },
  
  computed: {
    departmentSlug() {
      return this.$route.params.slug
    },
    
    availableYears() {
      const years = [...new Set(this.articles.map(article => {
        return article.conference_year?.year || 'Unknown'
      }))]
      return years.sort((a, b) => {
        if (a === 'Unknown') return 1
        if (b === 'Unknown') return -1
        return parseInt(b) - parseInt(a) // Sort descending (newest first)
      })
    },
    
    filteredArticles() {
      let filtered = this.articles

      // Filter by conference year
      if (this.selectedYear !== 'all') {
        filtered = filtered.filter(article => 
          article.conference_year?.year === this.selectedYear
        )
      }

      // Filter by search query
      if (this.searchQuery.trim()) {
        filtered = articleHelpers.filterArticles(filtered, {
          search: this.searchQuery.trim()
        })
      }

      // Sort by date (newest first)
      return articleHelpers.sortArticles(filtered, 'date', 'desc')
    },
    
    publicationsFromArticles() {
      return this.filteredArticles.map(article => ({
        id: article.id,
        title: article.title,
        authors: article.author_name,
        journal: article.conference_year ? 
          conferenceYearHelpers.formatConferenceYear(article.conference_year) : 
          'Conference Proceedings',
        year: article.conference_year?.year ? parseInt(article.conference_year.year) : new Date().getFullYear(),
        doi: `10.1234/article.${article.id}`,
        abstract: articleHelpers.formatArticleSummary(article),
        fullText: article.content || 'Full text not available.',
        created_at: article.created_at,
        conference_year: article.conference_year
      }))
    }
  },
  
  mounted() {
    this.fetchDepartmentData()
  },
  
  methods: {
    setActiveTab(tab) {
      this.activeTab = tab
      this.selectedArticle = null
      
      // Load articles when publications tab is opened
      if (tab === 'publications' && this.articles.length === 0) {
        this.fetchArticles()
      }
    },
    
    selectArticle(publication) {
      // Find the original article
      const article = this.articles.find(a => a.id === publication.id)
      if (article) {
        this.selectedArticle = article
      }
    },
    
    backToPublications() {
      this.selectedArticle = null
    },
    
    async fetchArticles() {
      try {
        this.loading = true
        this.error = null
        
        // Fetch articles with optional search
        const params = this.searchQuery.trim() ? { search: this.searchQuery.trim() } : undefined
        this.articles = await articleApi.getArticles(params)
        
      } catch (err) {
        this.error = 'Failed to load articles. Please try again later.'
        console.error('Error fetching articles:', err)
      } finally {
        this.loading = false
      }
    },
    
    async fetchConferenceYears() {
      try {
        this.conferenceYears = await conferenceYearApi.getConferenceYears()
      } catch (err) {
        console.error('Error fetching conference years:', err)
      }
    },
    
    async searchArticles() {
      if (this.searchQuery.trim()) {
        try {
          this.loading = true
          const result = await articleApi.searchArticles(this.searchQuery.trim())
          this.articles = result.articles || []
        } catch (err) {
          this.error = 'Search failed. Please try again.'
          console.error('Search error:', err)
        } finally {
          this.loading = false
        }
      } else {
        // If search is cleared, fetch all articles
        this.fetchArticles()
      }
    },
    
    clearSearch() {
      this.searchQuery = ''
      this.fetchArticles()
    },
    
    async fetchDepartmentData() {
      console.log(`Loading data for department: ${this.departmentSlug}`)
      
      // Fetch conference years for reference
      await this.fetchConferenceYears()
      
      // Articles will be loaded when publications tab is opened
      if (this.activeTab === 'publications') {
        await this.fetchArticles()
      }
    },
    
    // Helper methods for template
    formatConferenceYear(conferenceYear) {
      return conferenceYearHelpers.formatConferenceYear(conferenceYear)
    },
    
    formatDate(dateString) {
      return articleHelpers.formatDate(dateString)
    },
    
    formatArticleSummary(article) {
      return articleHelpers.formatArticleSummary(article)
    }
  }
}
</script>

<style scoped>
/* All existing styles remain exactly the same */
/* Base Styles */
.department-page {
  font-family: 'Open Sans', Arial, sans-serif;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Department Header */
.department-header {
  position: relative;
  overflow: hidden;
}

.department-image {
  position: relative;
  width: 100%;
  height: 40vh;
  max-height: 400px;
}

.department-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.department-title {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.6);
  color: white;
  padding: 1.5rem;
}

.department-title h1 {
  font-size: 2.5rem;
  margin: 0 0 0.5rem 0;
}

.department-title p {
  font-size: 1.1rem;
  margin: 0;
  max-width: 800px;
}

/* Department Navigation */
.department-nav {
  background-color: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 10;
}

.department-nav .container {
  display: flex;
  overflow-x: auto;
  white-space: nowrap;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none; /* Firefox */
}

.department-nav .container::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Edge */
}

.tab-button {
  background: none;
  border: none;
  padding: 1rem 1.5rem;
  font-size: 1rem;
  font-weight: 500;
  color: #555;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
}

.tab-button:hover {
  color: #3498db;
}

.tab-button.active {
  color: #3498db;
}

.tab-button.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background-color: #3498db;
}

/* Department Content */
.department-content {
  padding: 3rem 0;
}

.content-section {
  margin-bottom: 2rem;
}

.content-section h2 {
  font-size: 2rem;
  color: #333;
  margin-bottom: 2rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #f0f0f0;
}

/* Overview Tab */
.overview-content p {
  font-size: 1.1rem;
  line-height: 1.6;
  color: #555;
  margin-bottom: 2rem;
}

.overview-highlights {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.highlight-card {
  background-color: #f9f9f9;
  padding: 2rem;
  border-radius: 8px;
  text-align: center;
  transition: transform 0.3s ease;
}

.highlight-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.highlight-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.highlight-card h3 {
  font-size: 1.3rem;
  color: #333;
  margin-bottom: 0.5rem;
}

.highlight-card p {
  color: #666;
  margin: 0;
}

.department-stats {
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
  background-color: #3498db;
  color: white;
  padding: 2rem;
  border-radius: 8px;
  margin-top: 2rem;
}

.stat-item {
  text-align: center;
  padding: 1rem;
}

.stat-number {
  display: block;
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 1rem;
}

/* Research Areas Tab */
.research-areas-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.research-area-card {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
}

.research-area-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.research-area-card h3 {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 1rem;
}

.research-area-card p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.learn-more {
  color: #3498db;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
}

.learn-more:hover {
  text-decoration: underline;
}

.research-facilities {
  background-color: #f9f9f9;
  padding: 2rem;
  border-radius: 8px;
}

.research-facilities h3 {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 1rem;
}

.research-facilities p {
  color: #555;
  margin-bottom: 1rem;
}

.facilities-list {
  padding-left: 1.5rem;
  margin-bottom: 0;
}

.facilities-list li {
  margin-bottom: 0.5rem;
  color: #555;
}

/* Faculty Tab */
.faculty-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.faculty-card {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
}

.faculty-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.faculty-image {
  height: 200px;
  overflow: hidden;
}

.faculty-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.faculty-info {
  padding: 1.5rem;
}

.faculty-info h3 {
  font-size: 1.3rem;
  color: #333;
  margin: 0 0 0.5rem 0;
}

.faculty-title {
  color: #3498db;
  margin: 0 0 0.5rem 0;
  font-weight: 500;
}

.faculty-specialty {
  color: #666;
  margin: 0 0 1rem 0;
}

.faculty-profile-link {
  color: #3498db;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
}

.faculty-profile-link:hover {
  text-decoration: underline;
}

/* Programs Tab */
.programs-list {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  margin-bottom: 3rem;
}

.program-item {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
}

.program-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.program-item h3 {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 1rem;
}

.program-item p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.program-details {
  display: flex;
  gap: 1rem;
}

.program-link, .application-link {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.program-link {
  background-color: #3498db;
  color: white;
}

.application-link {
  background-color: transparent;
  color: #3498db;
  border: 1px solid #3498db;
}

.program-link:hover {
  background-color: #2980b9;
}

.application-link:hover {
  background-color: rgba(52, 152, 219, 0.1);
}

.additional-offerings {
  background-color: #f9f9f9;
  padding: 2rem;
  border-radius: 8px;
}

.additional-offerings h3 {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 1rem;
}

.offerings-list {
  padding-left: 1.5rem;
  margin-bottom: 0;
}

.offerings-list li {
  margin-bottom: 0.5rem;
  color: #555;
}

/* Publications Tab */
.publications-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.publication-item {
  background-color: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.3s ease;
}

.publication-item:hover {
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.publication-item h3 {
  font-size: 1.3rem;
  color: #333;
  margin: 0 0 0.75rem 0;
}

.publication-authors {
  color: #555;
  font-style: italic;
  margin: 0;
}

.publication-details {
  color: #666;
  margin: 0 0 0.5rem 0;
}

.publication-doi {
  color: #888;
  margin: 0 0 1rem 0;
  font-size: 0.9rem;
}

.publication-link {
  color: #3498db;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
}

.publication-link:hover {
  text-decoration: underline;
}

.publications-more {
  text-align: center;
  margin-top: 2rem;
}

.view-all-link {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background-color: #3498db;
  color: white;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.view-all-link:hover {
  background-color: #2980b9;
}

/* Contact Tab */
.contact-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
}

.contact-details {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.contact-group h3 {
  font-size: 1.2rem;
  color: #333;
  margin: 0 0 0.5rem 0;
}

.contact-group p {
  color: #555;
  margin: 0;
}

.contact-group a {
  color: #3498db;
  text-decoration: none;
}

.contact-group a:hover {
  text-decoration: underline;
}

.contact-form {
  background-color: #f9f9f9;
  padding: 2rem;
  border-radius: 8px;
}

.contact-form h3 {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #555;
  font-weight: 500;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  font-family: inherit;
}

.submit-button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.submit-button:hover {
  background-color: #2980b9;
}

/* Publications Tab Enhanced Styles */
.publications-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.publications-controls {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
  align-items: center;
}

.search-controls {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.search-input {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  min-width: 200px;
}

.search-button, .clear-button {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.search-button {
  background-color: #3498db;
  color: white;
}

.search-button:hover {
  background-color: #2980b9;
}

.clear-button {
  background-color: #e74c3c;
  color: white;
}

.clear-button:hover {
  background-color: #c0392b;
}

.year-filter {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.year-filter label {
  font-weight: 500;
  color: #555;
}

.year-dropdown {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  background-color: white;
  cursor: pointer;
}

.publication-abstract {
  color: #666;
  line-height: 1.5;
  margin: 1rem 0;
  font-style: italic;
}

.publication-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-top: 1rem;
}

.read-article-btn {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.read-article-btn:hover {
  background-color: #2980b9;
}

.external-link {
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
}

.external-link:hover {
  text-decoration: underline;
}

.loading-state, .error-state {
  text-align: center;
  padding: 2rem;
  background-color: #f9f9f9;
  border-radius: 8px;
  margin: 2rem 0;
}

.error-state {
  background-color: #ffeaea;
  color: #e74c3c;
}

.retry-button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 1rem;
}

.no-publications {
  text-align: center;
  padding: 2rem;
  background-color: #f9f9f9;
  border-radius: 8px;
  margin: 2rem 0;
}

.no-publications p {
  color: #666;
  font-size: 1.1rem;
  margin: 0;
}

.publications-summary {
  text-align: center;
  margin-top: 2rem;
  color: #666;
  font-style: italic;
}

/* Article Reader Styles */
.article-reader {
  max-width: 800px;
  margin: 0 auto;
}

.back-button {
  background: none;
  border: none;
  color: #3498db;
  cursor: pointer;
  font-size: 1rem;
  margin-bottom: 2rem;
  padding: 0.5rem 0;
  transition: color 0.3s ease;
}

.back-button:hover {
  color: #2980b9;
  text-decoration: underline;
}

.article-content {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.article-header {
  border-bottom: 2px solid #f0f0f0;
  padding-bottom: 2rem;
  margin-bottom: 2rem;
}

.article-header h1 {
  font-size: 2rem;
  color: #333;
  margin-bottom: 1rem;
  line-height: 1.3;
}

.article-meta {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.article-authors {
  font-size: 1.1rem;
  color: #555;
  font-style: italic;
  margin: 0;
}

.article-publication {
  color: #666;
  margin: 0;
}

.article-doi {
  color: #888;
  font-size: 0.9rem;
  margin: 0;
}

.article-date {
  color: #888;
  font-size: 0.9rem;
  margin: 0;
}

.publication-date {
  color: #888;
  margin: 0 0 0.5rem 0;
  font-size: 0.9rem;
}

.article-body {
  line-height: 1.7;
}

.abstract-section,
.fulltext-section {
  margin-bottom: 2rem;
}

.abstract-section h2,
.fulltext-section h2 {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e0e0e0;
}

.abstract-section p {
  font-size: 1.1rem;
  color: #555;
  font-style: italic;
  background-color: #f9f9f9;
  padding: 1.5rem;
  border-radius: 8px;
  border-left: 4px solid #3498db;
}

.article-paragraph {
  margin-bottom: 1.5rem;
  color: #444;
  text-align: justify;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .publications-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-controls {
    justify-content: center;
  }
  
  .search-input {
    min-width: auto;
    flex: 1;
  }
  
  .year-filter {
    justify-content: center;
  }
  
  .publication-actions {
    flex-direction: column;
    align-items: stretch;
  }
  
  .read-article-btn,
  .external-link {
    text-align: center;
    width: 100%;
  }
  
  .article-content {
    padding: 1rem;
  }
  
  .article-header h1 {
    font-size: 1.5rem;
  }
}
</style>