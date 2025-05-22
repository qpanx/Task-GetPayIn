import axios from 'axios';

const state = {
  logs: [],
  summary: null,
  loading: false,
  pagination: {
    currentPage: 1,
    lastPage: 1,
    perPage: 20,
    total: 0
  },
  filters: {
    action: null,
    entity_type: null,
    from_date: null,
    to_date: null
  }
};

const getters = {
  allLogs: state => state.logs,
  summary: state => state.summary,
  loading: state => state.loading,
  pagination: state => state.pagination,
  filters: state => state.filters
};

const actions = {
  async fetchLogs({ commit, state }) {
    try {
      commit('SET_LOADING', true);
      
      let query = `?page=${state.pagination.currentPage}`;
      if (state.filters.action) {
        query += `&action=${state.filters.action}`;
      }
      if (state.filters.entity_type) {
        query += `&entity_type=${state.filters.entity_type}`;
      }
      if (state.filters.from_date) {
        query += `&from_date=${state.filters.from_date}`;
      }
      if (state.filters.to_date) {
        query += `&to_date=${state.filters.to_date}`;
      }
      
      const response = await axios.get(`/activity-logs${query}`);
      
      commit('SET_LOGS', response.data.data);
      commit('SET_PAGINATION', {
        currentPage: response.data.current_page,
        lastPage: response.data.last_page,
        perPage: response.data.per_page,
        total: response.data.total
      });
      
      return response;
    } catch (error) {
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },
  
  async fetchSummary({ commit }) {
    try {
      commit('SET_LOADING', true);
      const response = await axios.get('/activity-logs/summary');
      commit('SET_SUMMARY', response.data);
      return response;
    } catch (error) {
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },
  
  setFilters({ commit, dispatch }, filters) {
    commit('SET_FILTERS', filters);
    // Reset to page 1 when filters change
    commit('SET_PAGINATION', { ...state.pagination, currentPage: 1 });
    dispatch('fetchLogs');
  },
  
  setPage({ commit, dispatch }, page) {
    commit('SET_PAGINATION', { ...state.pagination, currentPage: page });
    dispatch('fetchLogs');
  }
};

const mutations = {
  SET_LOADING(state, loading) {
    state.loading = loading;
  },
  
  SET_LOGS(state, logs) {
    state.logs = logs;
  },
  
  SET_SUMMARY(state, summary) {
    state.summary = summary;
  },
  
  SET_PAGINATION(state, pagination) {
    state.pagination = { ...state.pagination, ...pagination };
  },
  
  SET_FILTERS(state, filters) {
    state.filters = { ...state.filters, ...filters };
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}; 