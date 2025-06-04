import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import AdminDashboardView from '../views/AdminDashboardView.vue'
import DepartmentView from '../views/DepartmentView.vue'
import EditDashboardView from '../views/EditDashboardView.vue'
import ArticleView from '../views/ArticleView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
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
    },
    {
      path:'/admin/dashboard',
      name: 'dashboard',
      component: AdminDashboardView
    },
    // Department routes
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
    // Publications routes
    {
      path: '/publications',
      name: 'publications',
      component: () => import('../views/PublicationsView.vue'),
    },
    // Article detail route
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
      props: true
    },
  ],
})

export default router