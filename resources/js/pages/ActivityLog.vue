<template>
  <div>
    <h1 class="text-2xl font-semibold text-gray-900">Activity Log</h1>
    <p class="mt-1 text-sm text-gray-500">
      View recent activity and actions taken in your account.
    </p>

    <!-- Activity summary -->
    <div class="mt-6">
      <h2 class="text-lg font-medium text-gray-900">Activity Summary</h2>
      <div v-if="loadingSummary" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <div v-else class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-2">
        <!-- Entity Type Chart -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Activity by Entity Type</h3>
            <div class="mt-6">
              <div v-for="item in summary.entity_type_counts" :key="item.entity_type" class="flex items-center mt-2">
                <div class="w-24 text-sm font-medium text-gray-600">
                  {{ item.entity_type }}
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 ml-2">
                  <div 
                    class="bg-blue-600 h-4 rounded-full" 
                    :style="{ width: `${calculatePercentage(item.count, maxEntityTypeCount)}%` }"
                  ></div>
                </div>
                <div class="ml-2 w-8 text-sm text-gray-700">{{ item.count }}</div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Action Type Chart -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Activity by Action</h3>
            <div class="mt-6">
              <div v-for="item in summary.action_counts" :key="item.action" class="flex items-center mt-2">
                <div class="w-24 text-sm font-medium text-gray-600">
                  {{ item.action }}
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 ml-2">
                  <div 
                    class="bg-green-600 h-4 rounded-full" 
                    :style="{ width: `${calculatePercentage(item.count, maxActionCount)}%` }"
                  ></div>
                </div>
                <div class="ml-2 w-8 text-sm text-gray-700">{{ item.count }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity log filters -->
    <div class="mt-8">
      <h2 class="text-lg font-medium text-gray-900">Activity Log</h2>
      <div class="mt-2 grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div>
          <label for="filter-action" class="block text-sm font-medium text-gray-700">Action</label>
          <select
            id="filter-action"
            v-model="filters.action"
            @change="applyFilters"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
          >
            <option value="">All Actions</option>
            <option value="create">Create</option>
            <option value="update">Update</option>
            <option value="delete">Delete</option>
            <option value="publish">Publish</option>
            <option value="activate">Activate</option>
            <option value="deactivate">Deactivate</option>
          </select>
        </div>
        <div>
          <label for="filter-entity-type" class="block text-sm font-medium text-gray-700">Entity Type</label>
          <select
            id="filter-entity-type"
            v-model="filters.entity_type"
            @change="applyFilters"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
          >
            <option value="">All Entities</option>
            <option value="post">Post</option>
            <option value="platform">Platform</option>
          </select>
        </div>
        <div>
          <label for="filter-from-date" class="block text-sm font-medium text-gray-700">From Date</label>
          <input
            type="date"
            id="filter-from-date"
            v-model="filters.from_date"
            @change="applyFilters"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
          />
        </div>
        <div>
          <label for="filter-to-date" class="block text-sm font-medium text-gray-700">To Date</label>
          <input
            type="date"
            id="filter-to-date"
            v-model="filters.to_date"
            @change="applyFilters"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
          />
        </div>
      </div>
    </div>

    <!-- Activity log list -->
    <div class="mt-4">
      <div v-if="loading" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <div v-else-if="logs.length === 0" class="text-center py-12 text-gray-500">
        No activity logs found with the current filters.
      </div>
      <div v-else>
        <div class="flex flex-col">
          <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
              <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Action
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Entity Type
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Details
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="log in logs" :key="log.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ formatDate(log.created_at) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span 
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                          :class="getActionClass(log.action)"
                        >
                          {{ log.action }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ log.entity_type }}
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-600">
                        <span v-if="log.meta_data && log.meta_data.title">
                          Post: {{ log.meta_data.title }}
                        </span>
                        <span v-else-if="log.meta_data && log.meta_data.platform_name">
                          Platform: {{ log.meta_data.platform_name }}
                        </span>
                        <span v-else-if="log.meta_data && log.meta_data.platforms">
                          Platforms: {{ log.meta_data.platforms.join(', ') }}
                        </span>
                        <span v-else>
                          ID: {{ log.entity_id }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.lastPage > 1" class="mt-4 flex justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ (pagination.currentPage - 1) * pagination.perPage + 1 }}</span>
              to
              <span class="font-medium">{{ Math.min(pagination.currentPage * pagination.perPage, pagination.total) }}</span>
              of
              <span class="font-medium">{{ pagination.total }}</span>
              results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="setPage(pagination.currentPage - 1)"
                :disabled="pagination.currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.currentPage === 1 }"
              >
                Previous
              </button>
              <button
                v-for="page in pageRange"
                :key="page"
                @click="setPage(page)"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium hover:bg-gray-50"
                :class="page === pagination.currentPage ? 'bg-blue-50 text-blue-600' : 'text-gray-700'"
              >
                {{ page }}
              </button>
              <button
                @click="setPage(pagination.currentPage + 1)"
                :disabled="pagination.currentPage === pagination.lastPage"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.currentPage === pagination.lastPage }"
              >
                Next
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';

export default {
  name: 'ActivityLogPage',
  setup() {
    const store = useStore();
    const loading = ref(true);
    const loadingSummary = ref(true);
    
    // Get state from store
    const logs = computed(() => store.getters['activityLog/allLogs']);
    const summary = computed(() => store.getters['activityLog/summary'] || {
      entity_type_counts: [],
      action_counts: [],
      recent_activity: []
    });
    const pagination = computed(() => store.getters['activityLog/pagination']);
    const filters = computed(() => store.getters['activityLog/filters']);
    
    // Calculate max counts for charts
    const maxEntityTypeCount = computed(() => {
      if (!summary.value.entity_type_counts || summary.value.entity_type_counts.length === 0) return 0;
      return Math.max(...summary.value.entity_type_counts.map(item => item.count));
    });
    
    const maxActionCount = computed(() => {
      if (!summary.value.action_counts || summary.value.action_counts.length === 0) return 0;
      return Math.max(...summary.value.action_counts.map(item => item.count));
    });
    
    // Calculate page range for pagination
    const pageRange = computed(() => {
      const range = [];
      const totalPages = pagination.value.lastPage;
      const currentPage = pagination.value.currentPage;
      
      // Show max 5 pages
      const maxPages = 5;
      let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
      let endPage = Math.min(totalPages, startPage + maxPages - 1);
      
      // Adjust if we're near the end
      if (endPage - startPage < maxPages - 1) {
        startPage = Math.max(1, endPage - maxPages + 1);
      }
      
      for (let i = startPage; i <= endPage; i++) {
        range.push(i);
      }
      
      return range;
    });
    
    // Helper functions
    const calculatePercentage = (count, max) => {
      if (max === 0) return 0;
      return (count / max) * 100;
    };
    
    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return date.toLocaleString();
    };
    
    const getActionClass = (action) => {
      switch (action) {
        case 'create':
          return 'bg-green-100 text-green-800';
        case 'update':
          return 'bg-blue-100 text-blue-800';
        case 'delete':
          return 'bg-red-100 text-red-800';
        case 'publish':
          return 'bg-purple-100 text-purple-800';
        case 'activate':
          return 'bg-teal-100 text-teal-800';
        case 'deactivate':
          return 'bg-orange-100 text-orange-800';
        default:
          return 'bg-gray-100 text-gray-800';
      }
    };
    
    // Actions
    const loadLogs = async () => {
      loading.value = true;
      try {
        await store.dispatch('activityLog/fetchLogs');
      } catch (error) {
        console.error('Error loading activity logs:', error);
      } finally {
        loading.value = false;
      }
    };
    
    const loadSummary = async () => {
      loadingSummary.value = true;
      try {
        await store.dispatch('activityLog/fetchSummary');
      } catch (error) {
        console.error('Error loading activity summary:', error);
      } finally {
        loadingSummary.value = false;
      }
    };
    
    const applyFilters = () => {
      store.dispatch('activityLog/setFilters', filters.value);
    };
    
    const setPage = (page) => {
      if (page < 1 || page > pagination.value.lastPage) return;
      store.dispatch('activityLog/setPage', page);
    };
    
    onMounted(() => {
      loadLogs();
      loadSummary();
    });
    
    return {
      logs,
      summary,
      loading,
      loadingSummary,
      pagination,
      filters,
      pageRange,
      maxEntityTypeCount,
      maxActionCount,
      applyFilters,
      setPage,
      calculatePercentage,
      formatDate,
      getActionClass
    };
  }
};
</script> 