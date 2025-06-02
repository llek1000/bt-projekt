import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
//import ArticleView from '../views/ArticleView.vue'
import LoginView from '../views/LoginView.vue'
import AdminDashboardView from '../views/AdminDashboardView.vue'
import DepartmentView from '../views/DepartmentView.vue'
import EditDashboardView from '../views/EditDashboardView.vue'
//import ConferenceView from '@/views/ConferenceView.vue'

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
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
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
    {
      path: '/edit/dashboard',
      name: 'editDashboard',
      component: EditDashboardView,
      props: true
    },
  /*
    {
      path: '/article',
      name: 'article',
      component: ArticleView,
    },
    {
      path: '/conference',
      name: 'conference',
      component: ConferenceView,
    }*/
  ],
})

export default router