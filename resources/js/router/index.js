import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/kredyty-gotowkowe',
    name: 'Loans',
    component: () => import('../views/Loans.vue')
  },
  {
    path: '/kredyty-hipoteczne',
    name: 'Mortgages',
    component: () => import('../views/Mortgages.vue')
  },
  {
    path: '/konta-osobiste',
    name: 'Accounts',
    component: () => import('../views/Accounts.vue')
  },
  {
    path: '/lokaty',
    name: 'Deposits',
    component: () => import('../views/Deposits.vue')
  },
  {
    path: '/blog',
    name: 'Blog',
    component: () => import('../views/Blog.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router 