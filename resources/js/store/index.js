import { createStore } from 'vuex';
import auth from './modules/auth';
import posts from './modules/posts';
import platforms from './modules/platforms';
import activityLog from './modules/activityLog';

export default createStore({
  modules: {
    auth,
    posts,
    platforms,
    activityLog
  }
}); 