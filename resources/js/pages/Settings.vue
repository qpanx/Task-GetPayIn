<template>
  <div>
    <h1 class="text-2xl font-semibold text-gray-900">Platform Settings</h1>
    <p class="mt-1 text-sm text-gray-500">
      Manage your social media platforms and connection settings.
    </p>

    <div v-if="loading" class="mt-6 text-center py-12">
      <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    <div v-else class="mt-6">
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
          <li v-for="platform in platforms" :key="platform.id">
            <div class="px-4 py-4 sm:px-6">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                      <span class="text-white font-bold">{{ platform.name.charAt(0) }}</span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ platform.name }}</h3>
                    <p class="text-sm text-gray-500">{{ platform.type }}</p>
                  </div>
                </div>
                <div>
                  <button 
                    @click="togglePlatform(platform.id)"
                    :class="[
                      'px-4 py-2 rounded-md text-sm font-medium',
                      isPlatformActive(platform.id) 
                        ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                        : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                    ]"
                  >
                    {{ isPlatformActive(platform.id) ? 'Active' : 'Inactive' }}
                  </button>
                </div>
              </div>

              <div v-if="isPlatformActive(platform.id)" class="mt-4">
                <h4 class="text-sm font-medium text-gray-700">Platform Credentials</h4>
                <div class="mt-2 border border-gray-200 rounded-md p-4">
                  <div v-if="platform.type === 'twitter'" class="space-y-3">
                    <div>
                      <label for="api_key" class="block text-xs font-medium text-gray-700">API Key</label>
                      <input 
                        type="text" 
                        :id="`${platform.id}_api_key`" 
                        v-model="platformCredentials[platform.id].api_key" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label for="api_secret" class="block text-xs font-medium text-gray-700">API Secret</label>
                      <input 
                        type="password" 
                        :id="`${platform.id}_api_secret`" 
                        v-model="platformCredentials[platform.id].api_secret" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label for="access_token" class="block text-xs font-medium text-gray-700">Access Token</label>
                      <input 
                        type="password" 
                        :id="`${platform.id}_access_token`" 
                        v-model="platformCredentials[platform.id].access_token" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                    </div>
                  </div>
                  <div v-else-if="platform.type === 'facebook' || platform.type === 'instagram'" class="space-y-3">
                    <div>
                      <label for="app_id" class="block text-xs font-medium text-gray-700">App ID</label>
                      <input 
                        type="text" 
                        :id="`${platform.id}_app_id`" 
                        v-model="platformCredentials[platform.id].app_id" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label for="app_secret" class="block text-xs font-medium text-gray-700">App Secret</label>
                      <input 
                        type="password" 
                        :id="`${platform.id}_app_secret`" 
                        v-model="platformCredentials[platform.id].app_secret" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label for="access_token" class="block text-xs font-medium text-gray-700">Access Token</label>
                      <input 
                        type="password" 
                        :id="`${platform.id}_access_token`" 
                        v-model="platformCredentials[platform.id].access_token" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                    </div>
                  </div>
                  <div v-else class="space-y-3">
                    <div>
                      <label for="api_key" class="block text-xs font-medium text-gray-700">API Key</label>
                      <input 
                        type="text" 
                        :id="`${platform.id}_api_key`" 
                        v-model="platformCredentials[platform.id].api_key" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label for="api_secret" class="block text-xs font-medium text-gray-700">API Secret</label>
                      <input 
                        type="password" 
                        :id="`${platform.id}_api_secret`" 
                        v-model="platformCredentials[platform.id].api_secret" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                    </div>
                  </div>
                  
                  <div class="mt-4">
                    <button 
                      @click="saveCredentials(platform.id)"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      Save Credentials
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="platform.requirements" class="mt-2">
                <div class="text-sm text-gray-500">
                  <span class="font-medium">Requirements:</span>
                  <span v-if="platform.requirements.character_limit"> Character limit: {{ platform.requirements.character_limit }}</span>
                  <span v-if="platform.requirements.image_required"> | Image required</span>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import { useStore } from 'vuex';

export default {
  name: 'SettingsPage',
  setup() {
    const store = useStore();
    const loading = ref(true);
    const platformCredentials = ref({});
    
    const platforms = computed(() => store.getters['platforms/allPlatforms']);
    const userPlatforms = computed(() => store.getters['platforms/userPlatforms']);
    
    const isPlatformActive = (platformId) => {
      const platform = userPlatforms.value.find(p => p.id === platformId);
      return platform && platform.pivot.is_active;
    };
    
    const initializeCredentials = () => {
      platforms.value.forEach(platform => {
        const userPlatform = userPlatforms.value.find(p => p.id === platform.id);
        
        if (userPlatform && userPlatform.pivot.credentials) {
          platformCredentials.value[platform.id] = JSON.parse(JSON.stringify(userPlatform.pivot.credentials));
        } else {
          // Default empty credentials based on platform type
          if (platform.type === 'twitter') {
            platformCredentials.value[platform.id] = {
              api_key: '',
              api_secret: '',
              access_token: ''
            };
          } else if (platform.type === 'facebook' || platform.type === 'instagram') {
            platformCredentials.value[platform.id] = {
              app_id: '',
              app_secret: '',
              access_token: ''
            };
          } else {
            platformCredentials.value[platform.id] = {
              api_key: '',
              api_secret: ''
            };
          }
        }
      });
    };
    
    const loadData = async () => {
      loading.value = true;
      try {
        await store.dispatch('platforms/fetchPlatforms');
        await store.dispatch('platforms/fetchUserPlatforms');
        initializeCredentials();
      } catch (error) {
        console.error('Error loading platforms:', error);
      } finally {
        loading.value = false;
      }
    };
    
    const togglePlatform = async (platformId) => {
      try {
        await store.dispatch('platforms/togglePlatform', platformId);
      } catch (error) {
        console.error('Error toggling platform:', error);
      }
    };
    
    const saveCredentials = async (platformId) => {
      try {
        await store.dispatch('platforms/updateCredentials', {
          platformId,
          credentials: platformCredentials.value[platformId]
        });
        alert('Credentials saved successfully!');
      } catch (error) {
        console.error('Error saving credentials:', error);
        alert('Failed to save credentials. Please try again.');
      }
    };
    
    onMounted(() => {
      loadData();
    });
    
    return {
      loading,
      platforms,
      userPlatforms,
      platformCredentials,
      isPlatformActive,
      togglePlatform,
      saveCredentials
    };
  }
};
</script> 