import { createRouter, createWebHistory } from 'vue-router';
import store from '../store';

// Layouts
import AuthLayout from '../layouts/AuthLayout.vue';
import DashboardLayout from '../layouts/DashboardLayout.vue';

// Auth pages
import Login from '../pages/auth/Login.vue';
import Register from '../pages/auth/Register.vue';

// Dashboard pages
import Dashboard from '../pages/Dashboard.vue';
import PostsList from '../pages/posts/PostsList.vue';
import PostEditor from '../pages/posts/PostEditor.vue';
import Settings from '../pages/Settings.vue';
import ActivityLog from '../pages/ActivityLog.vue';

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/',
    component: AuthLayout,
    meta: { guest: true },
    children: [
      {
        path: '/login',
        name: 'Login',
        component: Login
      },
      {
        path: '/register',
        name: 'Register',
        component: Register
      }
    ]
  },
  {
    path: '/',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard
      },
      {
        path: '/posts',
        name: 'Posts',
        component: PostsList
      },
      {
        path: '/posts/create',
        name: 'CreatePost',
        component: PostEditor
      },
      {
        path: '/posts/:id/edit',
        name: 'EditPost',
        component: PostEditor,
        props: route => ({ postId: parseInt(route.params.id) })
      },
      {
        path: '/settings',
        name: 'Settings',
        component: Settings
      },
      {
        path: '/activity',
        name: 'ActivityLog',
        component: ActivityLog
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guards
router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!store.getters['auth/isAuthenticated']) {
      next({ name: 'Login' });
    } else {
      next();
    }
  } else if (to.matched.some(record => record.meta.guest)) {
    if (store.getters['auth/isAuthenticated']) {
      next({ name: 'Dashboard' });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router; 