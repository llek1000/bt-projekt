import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import AdminDashboardView from '../views/AdminDashboardView.vue'
import DepartmentView from '../views/DepartmentView.vue'
import EditDashboardView from '../views/EditDashboardView.vue'
import ArticleView from '../views/ArticleView.vue'
import auth from '@/services/authentification'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    return { top: 0, behavior: 'smooth' }
  },
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      beforeEnter: (to, from, next) => {
        if (auth.isAuthenticated()) {
          next('/')
        } else {
          next()
        }
      }
    },
    {
      path: '/admin/dashboard',
      name: 'dashboard',
      component: AdminDashboardView,
      meta: { requiresAuth: true, requiredRole: 'admin' }
    },
    {
      path: '/departments',
      name: 'departments',
      component: () => import('../views/DepartmentView.vue'),
    },
    {
      path: '/departments/:slug',
      name: 'department',
      component: DepartmentView,
      props: true
    }, 
    {
      path: '/publications',
      name: 'publications',
      component: () => import('../views/PublicationsView.vue'),
    },
    {
      path: '/articles/:id',
      name: 'article',
      component: ArticleView,
      props: true
    },
    {
      path: '/edit/dashboard',
      name: 'editDashboard',
      component: EditDashboardView,
      meta: { requiresAuth: true, requiredRole: 'editor' }
    },
  ],
})

router.beforeEach(async (to, from, next) => {
  const isAuthenticated = auth.isAuthenticated()
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    localStorage.setItem('redirectAfterLogin', to.fullPath)
    next('/login')
    return
  }
  
  if (to.meta.requiredRole && isAuthenticated) {
    try {
      const response = await fetch('http://localhost/bt-projekt/public/api/user', {
        headers: {
          'Authorization': `Bearer ${auth.getToken()}`,
          'Content-Type': 'application/json'
        }
      })
      
      if (response.ok) {
        const data = await response.json()
        const user = data.user
        
        const hasRequiredRole = user.roles?.some((role: any) => 
          typeof role.name === 'string' && role.name.toLowerCase() === String(to.meta.requiredRole).toLowerCase()
        )
        
        if (!hasRequiredRole) {
          const userRoles = user.roles?.map((role: any) => role.name.toLowerCase()) || []
          
          if (userRoles.includes('admin')) {
            next('/admin/dashboard')
          } else if (userRoles.includes('editor')) {
            next('/edit/dashboard')
          } else {
            next('/')
          }
          return
        }
      } else {
        auth.forceLogout()
        next('/login')
        return
      }
    } catch (error) {
      console.error('Chyba pri kontrole role používateľa:', error)
      auth.forceLogout()
      next('/login')
      return
    }
  }
  
  next()
})

export default router