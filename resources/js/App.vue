<template>
  <div class="h-screen flex flex-col">
    <!-- Navigation -->
    <nav v-if="isAuthenticated" class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-blue-600">Content Scheduler</h1>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link to="/dashboard" class="border-transparent text-gray-500 hover:border-blue-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Dashboard
              </router-link>
              <router-link to="/posts" class="border-transparent text-gray-500 hover:border-blue-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Posts
              </router-link>
              <router-link to="/settings" class="border-transparent text-gray-500 hover:border-blue-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Settings
              </router-link>
              <router-link to="/activity" class="border-transparent text-gray-500 hover:border-blue-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Activity
              </router-link>
            </div>
          </div>
          <div class="ml-3 relative flex items-center">
            <button @click="logout" class="ml-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <main class="flex-grow">
      <router-view></router-view>
    </main>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
  name: 'App',
  computed: {
    ...mapGetters({
      isAuthenticated: 'auth/isAuthenticated',
      user: 'auth/user'
    })
  },
  methods: {
    ...mapActions({
      logoutAction: 'auth/logout'
    }),
    async logout() {
      await this.logoutAction();
      this.$router.push('/login');
    }
  },
  created() {
    // Check if user is authenticated on page load
    const token = localStorage.getItem('token');
    if (token) {
      this.$store.dispatch('auth/getUser');
    } else if (this.$route.path !== '/login' && this.$route.path !== '/register') {
      this.$router.push('/login');
    }
  }
};
</script> 