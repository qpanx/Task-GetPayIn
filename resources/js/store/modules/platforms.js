import axios from 'axios';

const state = {
  platforms: [],
  userPlatforms: [],
  loading: false,
};

const getters = {
  allPlatforms: state => state.platforms,
  userPlatforms: state => state.userPlatforms,
  loading: state => state.loading,
};

const actions = {
  async fetchPlatforms({ commit }) {
    try {
      commit('SET_LOADING', true);
      const response = await axios.get('/platforms');
      commit('SET_PLATFORMS', response.data);
      return response;
    } catch (error) {
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },
  
  async fetchUserPlatforms({ commit }) {
    try {
      commit('SET_LOADING', true);
      const response = await axios.get('/user/platforms');
      commit('SET_USER_PLATFORMS', response.data);
      return response;
    } catch (error) {
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },
  
  async togglePlatform({ commit }, platformId) {
    try {
      commit('SET_LOADING', true);
      const response = await axios.post(`/platforms/${platformId}/toggle`);
      commit('UPDATE_USER_PLATFORM', response.data);
      return response;
    } catch (error) {
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },
  
  async updateCredentials({ commit }, { platformId, credentials }) {
    try {
      commit('SET_LOADING', true);
      const response = await axios.post(`/platforms/${platformId}/credentials`, { credentials });
      return response;
    } catch (error) {
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },
};

const mutations = {
  SET_LOADING(state, loading) {
    state.loading = loading;
  },
  
  SET_PLATFORMS(state, platforms) {
    state.platforms = platforms;
  },
  
  SET_USER_PLATFORMS(state, platforms) {
    state.userPlatforms = platforms;
  },
  
  UPDATE_USER_PLATFORM(state, data) {
    const index = state.userPlatforms.findIndex(p => p.id === data.platform.id);
    if (index !== -1) {
      state.userPlatforms[index] = { 
        ...state.userPlatforms[index],
        pivot: {
          ...state.userPlatforms[index].pivot,
          is_active: data.is_active
        }
      };
    } else {
      state.userPlatforms.push({
        ...data.platform,
        pivot: {
          platform_id: data.platform.id,
          user_id: state.user?.id,
          is_active: data.is_active,
          credentials: null
        }
      });
    }
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}; 