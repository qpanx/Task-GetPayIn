import axios from 'axios';

const state = {
  posts: [],
  post: null,
  loading: false,
  pagination: {
    currentPage: 1,
    lastPage: 1,
    perPage: 10,
    total: 0
  },
  filters: {
    status: null,
    date: null
  }
};

const getters = {
  allPosts: state => state.posts,
  currentPost: state => state.post,
  loading: state => state.loading,
  pagination: state => state.pagination,
  filters: state => state.filters
};

const actions = {
  async fetchPosts({ commit, state }) {
    try {
      commit('SET_LOADING', true);
      
      let query = `?page=${state.pagination.currentPage}`;
      if (state.filters.status) {
        query += `&status=${state.filters.status}`;
      }
      if (state.filters.date) {
        query += `&date=${state.filters.date}`;
      }
      
      const response = await axios.get(`/posts${query}`);
      
      commit('SET_POSTS', response.data.data);
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
  
  async fetchPost({ commit }, id) {
    try {
      commit('SET_LOADING', true);
      const response = await axios.get(`/posts/${id}`);
      commit('SET_POST', response.data);
      return response;
    } catch (error) {
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },
  
  async createPost({ commit }, postData) {
    try {
      commit('SET_LOADING', true);
      const response = await axios.post('/posts', postData);
      commit('ADD_POST', response.data);
      return response;
    } catch (error) {
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },
  
  async updatePost({ commit }, { id, postData }) {
    try {
      commit('SET_LOADING', true);
      const response = await axios.put(`/posts/${id}`, postData);
      commit('UPDATE_POST', response.data);
      return response;
    } catch (error) {
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },
  
  async deletePost({ commit }, id) {
    try {
      commit('SET_LOADING', true);
      await axios.delete(`/posts/${id}`);
      commit('REMOVE_POST', id);
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
    dispatch('fetchPosts');
  },
  
  setPage({ commit, dispatch }, page) {
    commit('SET_PAGINATION', { ...state.pagination, currentPage: page });
    dispatch('fetchPosts');
  }
};

const mutations = {
  SET_LOADING(state, loading) {
    state.loading = loading;
  },
  
  SET_POSTS(state, posts) {
    state.posts = posts;
  },
  
  SET_POST(state, post) {
    state.post = post;
  },
  
  ADD_POST(state, post) {
    state.posts.unshift(post);
  },
  
  UPDATE_POST(state, updatedPost) {
    const index = state.posts.findIndex(post => post.id === updatedPost.id);
    if (index !== -1) {
      state.posts.splice(index, 1, updatedPost);
    }
    // Also update current post if it matches
    if (state.post && state.post.id === updatedPost.id) {
      state.post = updatedPost;
    }
  },
  
  REMOVE_POST(state, id) {
    state.posts = state.posts.filter(post => post.id !== id);
    // Clear current post if it matches
    if (state.post && state.post.id === id) {
      state.post = null;
    }
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