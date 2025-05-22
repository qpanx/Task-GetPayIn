<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Top navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-blue-600">Content Scheduler</h1>
            </div>
            
            <!-- Desktop navigation links -->
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link 
                to="/dashboard" 
                class="border-transparent text-gray-500 hover:border-blue-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-blue-500 text-gray-900"
              >
                Dashboard
              </router-link>
              <router-link 
                to="/posts" 
                class="border-transparent text-gray-500 hover:border-blue-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-blue-500 text-gray-900"
              >
                Posts
              </router-link>
              <router-link 
                to="/settings" 
                class="border-transparent text-gray-500 hover:border-blue-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-blue-500 text-gray-900"
              >
                Settings
              </router-link>
              <router-link 
                to="/activity" 
                class="border-transparent text-gray-500 hover:border-blue-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-blue-500 text-gray-900"
              >
                Activity
              </router-link>
            </div>
          </div>
          
          <!-- User dropdown and mobile menu button -->
          <div class="ml-3 relative flex items-center">
            <div class="flex items-center">
              <span class="text-sm text-gray-700 mr-2">{{ userName }}</span>
              <button
                @click="logout"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page content -->
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'DashboardLayout',
  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
    userName() {
      return this.user ? this.user.name : '';
    }
  },
  methods: {
    ...mapActions({
      logoutAction: 'auth/logout'
    }),
    async logout() {
      await this.logoutAction();
      this.$router.push('/login');
    }
  }
}
</script> 