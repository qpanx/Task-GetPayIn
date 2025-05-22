<template>
  <div>
    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
    
    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Post statistics card -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Posts</dt>
                <dd>
                  <div class="text-lg font-medium text-gray-900">{{ stats.totalPosts }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <div class="text-sm">
            <router-link to="/posts" class="font-medium text-blue-600 hover:text-blue-500">View all posts</router-link>
          </div>
        </div>
      </div>

      <!-- Scheduled posts card -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Scheduled Posts</dt>
                <dd>
                  <div class="text-lg font-medium text-gray-900">{{ stats.scheduledPosts }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <div class="text-sm">
            <a href="#upcoming-posts" class="font-medium text-blue-600 hover:text-blue-500">View upcoming posts</a>
          </div>
        </div>
      </div>

      <!-- Published posts card -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Published Posts</dt>
                <dd>
                  <div class="text-lg font-medium text-gray-900">{{ stats.publishedPosts }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <div class="text-sm">
            <router-link to="/posts" class="font-medium text-blue-600 hover:text-blue-500">View all published</router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Upcoming scheduled posts -->
    <div class="mt-8" id="upcoming-posts">
      <h2 class="text-lg font-medium text-gray-900">Upcoming Scheduled Posts</h2>
      <div v-if="loading" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <div v-else-if="upcomingPosts.length === 0" class="text-center py-12 text-gray-500">
        No upcoming scheduled posts.
      </div>
      <div v-else class="mt-4 bg-white shadow sm:rounded-md">
        <ul class="divide-y divide-gray-200">
          <li v-for="post in upcomingPosts" :key="post.id">
            <div class="block hover:bg-gray-50">
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="truncate">
                    <div class="flex">
                      <p class="text-sm font-medium text-blue-600 truncate">{{ post.title }}</p>
                    </div>
                    <div class="mt-1 flex">
                      <p class="text-sm text-gray-600 truncate">{{ post.content }}</p>
                    </div>
                  </div>
                  <div class="ml-2 flex-shrink-0 flex">
                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                      {{ formatDate(post.scheduled_time) }}
                    </p>
                  </div>
                </div>
                <div class="mt-2 flex justify-between">
                  <div class="flex">
                    <span v-for="platform in post.platforms" :key="platform.id" class="mr-1 px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">
                      {{ platform.name }}
                    </span>
                  </div>
                  <div>
                    <router-link :to="`/posts/${post.id}/edit`" class="text-sm text-blue-600 hover:text-blue-500">
                      Edit
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="mt-8">
      <h2 class="text-lg font-medium text-gray-900">Quick Actions</h2>
      <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-2">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <h3 class="text-lg font-medium text-gray-900">Create New Post</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Create and schedule a new post for your social media platforms.
                </p>
              </div>
            </div>
            <div class="mt-6">
              <router-link to="/posts/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Post
              </router-link>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <h3 class="text-lg font-medium text-gray-900">Manage Platforms</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Configure your social media platforms and credentials.
                </p>
              </div>
            </div>
            <div class="mt-6">
              <router-link to="/settings" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Platform Settings
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

export default {
  name: 'DashboardPage',
  setup() {
    const loading = ref(true);
    const stats = ref({
      totalPosts: 0,
      scheduledPosts: 0,
      publishedPosts: 0
    });
    const upcomingPosts = ref([]);

    const fetchDashboardData = async () => {
      try {
        loading.value = true;
        
        // Get stats
        const statsResponse = await axios.get('/posts', { params: { statistics: true } });
        if (statsResponse.data.statistics) {
          stats.value = statsResponse.data.statistics;
        }
        
        // Get upcoming posts
        const postsResponse = await axios.get('/posts', { params: { status: 'scheduled', limit: 5 } });
        upcomingPosts.value = postsResponse.data.data || [];
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
      } finally {
        loading.value = false;
      }
    };

    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return date.toLocaleString();
    };

    onMounted(() => {
      fetchDashboardData();
    });

    return {
      loading,
      stats,
      upcomingPosts,
      formatDate
    };
  }
};
</script> 