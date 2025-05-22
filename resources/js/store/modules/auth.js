import axios from 'axios';

const state = {
  user: null,
  token: localStorage.getItem('token') || ''
};

const getters = {
  isAuthenticated: state => !!state.token,
  user: state => state.user
};

const actions = {
  async login({ commit }, credentials) {
    try {
      const response = await axios.post('/login', credentials);
      const token = response.data.access_token;
      localStorage.setItem('token', token);
      commit('SET_TOKEN', token);
      commit('SET_USER', response.data.user);
      return response;
    } catch (error) {
      throw error;
    }
  },
  
  async register({ commit }, userData) {
    try {
      const response = await axios.post('/register', userData);
      const token = response.data.access_token;
      localStorage.setItem('token', token);
      commit('SET_TOKEN', token);
      commit('SET_USER', response.data.user);
      return response;
    } catch (error) {
      throw error;
    }
  },
  
  async logout({ commit }) {
    try {
      await axios.post('/logout');
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      localStorage.removeItem('token');
      commit('CLEAR_AUTH');
    }
  },
  
  async getUser({ commit, state }) {
    try {
      const response = await axios.get('/user');
      commit('SET_USER', response.data);
      return response;
    } catch (error) {
      localStorage.removeItem('token');
      commit('CLEAR_AUTH');
      throw error;
    }
  }
};

const mutations = {
  SET_TOKEN(state, token) {
    state.token = token;
  },
  
  SET_USER(state, user) {
    state.user = user;
  },
  
  CLEAR_AUTH(state) {
    state.user = null;
    state.token = '';
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}; 